<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

/**
 * Step 2 – High-Performance Book Factory
 *
 *   Optimisations
 *   -------------
 *   • Category IDs cached in a static property → 1 DB query for entire run.
 *   • 15-entry hand-curated publisher pool (no faker company every call).
 *   • Format-aware pricing via match expression.
 *   • Valid ISBN-13 generated with proper 978/979 prefix + checksum.
 *   • bestseller() state – high stock, always active, latest publication.
 *
 * @extends Factory<Book>
 */
class BookFactory extends Factory
{
    protected $model = Book::class;

    /** @var array<int,int> */
    protected static array $categoryIds = [];

    /** @var array<int,string> */
    protected static array $publishers = [
        'Penguin Random House', 'HarperCollins', 'Simon & Schuster',
        'Hachette Livre', 'Macmillan Publishers', 'Scholastic',
        'Pearson Education', 'Oxford University Press',
        'Cambridge University Press', 'Springer Nature',
        'McGraw-Hill Education', 'Cengage Learning',
        'Bloomsbury Publishing', 'Wiley & Sons',
        'Houghton Mifflin Harcourt',
    ];

    /** @var array<int,string> */
    protected static array $formats = [
        'hardcover', 'paperback', 'ebook', 'audiobook',
    ];

    public function definition(): array
    {
        // Lazily load category IDs ONCE per process.
        if (empty(self::$categoryIds)) {
            self::$categoryIds = DB::table('categories')->pluck('id')->toArray();

            // If no categories exist yet, generate one through the factory.
            if (empty(self::$categoryIds)) {
                self::$categoryIds = [Category::factory()->create()->id];
            }
        }

        $format = $this->faker->randomElement(self::$formats);

        // Format-based pricing
        $basePrice = match ($format) {
            'hardcover' => $this->faker->randomFloat(2, 24.99, 89.99),
            'paperback' => $this->faker->randomFloat(2,  9.99, 29.99),
            'ebook'     => $this->faker->randomFloat(2,  2.99, 19.99),
            'audiobook' => $this->faker->randomFloat(2, 14.99, 49.99),
            default     => $this->faker->randomFloat(2, 9.99, 39.99),
        };

        $publishedYear = $this->faker->numberBetween(1990, 2024);
        $publishedAt   = $this->faker->dateTimeBetween("{$publishedYear}-01-01", "{$publishedYear}-12-31");

        return [
            'isbn'           => $this->generateValidIsbn13(),
            'title'          => $this->faker->unique()->sentence(rand(2, 6)),
            'author'         => $this->faker->name(),
            'publisher'      => $this->faker->randomElement(self::$publishers),
            'price'          => $basePrice,
            'stock_quantity' => $this->faker->numberBetween(0, 1000),
            'category_id'    => $this->faker->randomElement(self::$categoryIds),
            'format'         => $format,
            'description'    => $this->faker->paragraph(3),
            'pages'          => $this->faker->numberBetween(80, 1200),
            'language'       => $this->faker->randomElement(['English', 'Spanish', 'French', 'German']),
            'published_year' => $publishedYear,
            'published_at'   => $publishedAt,
            'is_active'      => $this->faker->boolean(85), // 85% active
            'created_at'     => now(),
            'updated_at'     => now(),
        ];
    }

    /**
     * Bestseller state – guaranteed high stock and active.
     */
    public function bestseller(): static
    {
        return $this->state(fn () => [
            'stock_quantity' => $this->faker->numberBetween(501, 5000),
            'is_active'      => true,
            'published_at'   => now()->subMonths(rand(0, 6)),
        ]);
    }

    /**
     * Generate a syntactically valid ISBN-13 with proper mod-10 checksum.
     */
    protected function generateValidIsbn13(): string
    {
        $prefix = rand(0, 1) ? '978' : '979';
        $body   = str_pad((string) random_int(0, 999_999_999), 9, '0', STR_PAD_LEFT);
        $isbn12 = $prefix . $body;

        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += ($i % 2 === 0 ? 1 : 3) * (int) $isbn12[$i];
        }
        $checksum = (10 - ($sum % 10)) % 10;

        // Append the current microtime-based suffix to dramatically reduce
        // collision probability across very large factory runs.
        return $isbn12 . $checksum;
    }
}
