
<style>
.footer {
    background: color-mix(in srgb, var(--pageturner-dark) 88%, #000 12%);
    color: #000;
    padding: 2.5rem 0;
    margin-top: 2.5rem;
    border-top: 1px solid rgba(0, 0, 0, 0.1);
}

.footer-container {
    max-width: 80rem;
    margin: 0 auto;
    padding: 0 1rem;
}

@media (min-width: 640px) {
    .footer-container {
        padding: 0 1.5rem;
    }
}

@media (min-width: 1024px) {
    .footer-container {
        padding: 0 2rem;
    }
}

.footer-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 2.5rem;
}

@media (min-width: 768px) {
    .footer-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (min-width: 1024px) {
    .footer-grid {
        grid-template-columns: repeat(4, 1fr);
    }
}

.brand-section {
    grid-column: span 1;
}

@media (min-width: 1024px) {
    .brand-section {
        grid-column: span 2;
    }
}

.brand-header {
    display: flex;
    align-items: center;
    margin-bottom: 1.5rem;
}

.brand-icon {
    width: 2.5rem;
    height: 2.5rem;
    background: linear-gradient(to bottom right, var(--pageturner-primary), var(--pageturner-secondary));
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
}

.brand-icon-svg {
    width: 1.5rem;
    height: 1.5rem;
    color: #fff;
}

.brand-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #fff;
    transition: color 0.3s;
    font-family: 'PageTurner', sans-serif;
}

.brand-title:hover {
    color: var(--pageturner-accent);
}

.brand-description {
    color: #d1d5db;
    margin-top: 1rem;
    max-width: 32rem;
    line-height: 1.625;
}

.social-links {
    margin-top: 2rem;
    display: flex;
    gap: 1.25rem;
}

.social-link {
    width: 2.5rem;
    height: 2.5rem;
    background: #1f2937;
    border-radius: 9999px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s;
    transform: translateY(0);
}

.social-link:hover {
    background: var(--pageturner-primary);
    transform: translateY(-0.25rem);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
}

.social-icon {
    width: 1.25rem;
    height: 1.25rem;
    color: #9ca3af;
    transition: color 0.3s;
}

.social-link:hover .social-icon {
    color: #fff;
}

.section-title {
    color: #ffffff;
    font-size: 1.25rem;
    font-weight: 700;
    margin-bottom: 1.5rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid rgb(255, 255, 255);
    font-family: 'PageTurner', sans-serif;
    display: flex;
    align-items: center;
}

.section-title-icon {
    width: 1.25rem;
    height: 1.25rem;
    margin-right: 0.5rem;
    color: var(--pageturner-accent);
}

.link-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.link-item {
    color: #d1d5db;
    transition: color 0.3s;
    display: flex;
    align-items: center;
    text-decoration: none;
}

.link-item:hover {
    color: var(--pageturner-accent);
}

.link-underline {
    width: 0.25rem;
    height: 1rem;
    background: var(--pageturner-accent);
    border-radius: 0 0.25rem 0.25rem 0;
    transform: scaleY(0);
    transition: transform 0.3s;
    margin-right: 0.75rem;
}

.link-item:hover .link-underline {
    transform: scaleY(1);
}

.link-icon {
    width: 1.25rem;
    height: 1.25rem;
    margin-right: 0.5rem;
    color: var(--pageturner-accent);
    opacity: 0.7;
}

.contact-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

.contact-item {
    display: flex;
    align-items: flex-start;
}

.contact-icon-wrapper {
    width: 2.5rem;
    height: 2.5rem;
    background: rgba(0, 0, 0, 0.5);
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    transition: background-color 0.3s;
}

.contact-item:hover .contact-icon-wrapper {
    background: rgba(0, 0, 0, 0.2);
}

.contact-icon {
    width: 1.25rem;
    height: 1.25rem;
    color: var(--pageturner-accent);
}

.contact-label {
    color: #9ca3af;
    font-size: 0.875rem;
    display: block;
}

.contact-link {
    color: #d1d5db;
    transition: color 0.3s;
    text-decoration: none;
    display: block;
}

.contact-link:hover {
    color: var(--pageturner-accent);
}

.contact-address {
    color: #d1d5db;
    display: block;
}

.newsletter-section {
    margin-top: 3rem;
    padding-top: 2.5rem;
    border-top: 1px solid rgba(0, 0, 0, 0.3);
}

.newsletter-container {
    max-width: 36rem;
    margin: 0 auto;
    text-align: center;
}

.newsletter-header {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1.5rem;
}

.newsletter-icon-wrapper {
    width: 2.5rem;
    height: 2.5rem;
    background: linear-gradient(to bottom right, var(--pageturner-primary), var(--pageturner-secondary));
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 0.75rem;
}

.newsletter-icon {
    width: 1.5rem;
    height: 1.5rem;
    color: #fff;
}

.newsletter-title {
    color: #ffffff;
    font-size: 1.25rem;
    font-weight: 700;
    font-family: 'PageTurner', sans-serif;
}

.newsletter-description {
    color: #d1d5db;
    margin-bottom: 2rem;
    max-width: 32rem;
    margin-left: auto;
    margin-right: auto;
    line-height: 1.625;
}

.newsletter-form {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    max-width: 32rem;
    margin: 0 auto;
}

@media (min-width: 640px) {
    .newsletter-form {
        flex-direction: row;
    }
}

.newsletter-input {
    flex: 1;
    padding: 0.75rem 1.5rem;
    border-radius: 0.75rem;
    background: rgba(31, 41, 55, 0.5);
    color: #fff;
    border: 2px solid rgba(0, 0, 0, 0.3);
    transition: all 0.3s;
    backdrop-filter: blur(4px);
}

.newsletter-input::placeholder {
    color: #9ca3af;
}

.newsletter-input:focus {
    outline: none;
    border-color: var(--pageturner-accent);
    box-shadow: 0 0 0 2px rgba(0, 0, 0, 0.2);
}

.newsletter-button {
    background: linear-gradient(to right, var(--pageturner-primary), var(--pageturner-secondary));
    color: #fff;
    padding: 0.75rem 2rem;
    border-radius: 0.75rem;
    font-weight: 700;
    border: none;
    transition: all 0.3s;
    transform: translateY(0);
    cursor: pointer;
}

.newsletter-button:hover {
    background: linear-gradient(to right, var(--pageturner-secondary), var(--pageturner-primary));
    transform: translateY(-0.25rem);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
}

.newsletter-footer {
    color: #9ca3af;
    font-size: 0.875rem;
    margin-top: 1.5rem;
}

.policies-section {
    margin-top: 3rem;
    padding-top: 2rem;
    border-top: 1px solid rgba(0, 0, 0, 0.3);
}

.policies-container {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    align-items: center;
}

@media (min-width: 768px) {
    .policies-container {
        flex-direction: row;
    }
}

.copyright {
    color: #9ca3af;
    font-size: 0.875rem;
    margin-bottom: 1.5rem;
    text-align: center;
}

@media (min-width: 768px) {
    .copyright {
        margin-bottom: 0;
        text-align: left;
    }
}

.copyright-heart {
    margin-top: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

@media (min-width: 768px) {
    .copyright-heart {
        justify-content: flex-start;
    }
}

.heart-icon {
    width: 1rem;
    height: 1rem;
    margin-right: 0.25rem;
    color: var(--pageturner-accent);
}

.policy-links {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 1.5rem;
    color: #9ca3af;
    font-size: 0.875rem;
}

.policy-link {
    color: #9ca3af;
    text-decoration: none;
    transition: color 0.3s;
}

.policy-link:hover {
    color: var(--pageturner-accent);
}

.quote-section {
    margin-top: 2.5rem;
    padding-top: 1.5rem;
    border-top: 1px solid rgba(0, 0, 0, 0.2);
    text-align: center;
}

.quote-text {
    color: #6b7280;
    font-size: 0.875rem;
    font-style: italic;
}

/* Utility classes */
.sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border-width: 0;
}
</style>

<footer class="footer">
    <div class="footer-container">
        <div class="footer-grid">
            <!-- Brand Information -->
            <div class="brand-section">
                <div class="brand-header">
                    <div class="brand-icon">
                        <svg class="brand-icon-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <a href="{{ route('home') }}" class="brand-title">
                        PageTurner Bookstore
                    </a>
                </div>
                
                <p class="brand-description">
                    Your literary sanctuary since 2023. Discover worlds within pages, expand your horizons, 
                    and find your next unforgettable read in our carefully curated collection.
                </p>
                
                <div class="social-links">
                    <a href="#" class="social-link">
                        <span class="sr-only">Facebook</span>
                        <svg class="social-icon" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" />
                        </svg>
                    </a>
                    <a href="#" class="social-link">
                        <span class="sr-only">Twitter</span>
                        <svg class="social-icon" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" />
                        </svg>
                    </a>
                    <a href="#" class="social-link">
                        <span class="sr-only">Instagram</span>
                        <svg class="social-icon" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.900 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd" />
                        </svg>
                    </a>
                    <a href="#" class="social-link">
                        <span class="sr-only">Goodreads</span>
                        <svg class="social-icon" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Quick Links -->
            <div>
                <h3 class="section-title" >
                    <svg class="section-title-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Quick Links
                </h3>
                <ul class="link-list">
                    <li>
                        <a href="{{ route('home') }}" class="link-item">
                            <div class="link-underline"></div>
                            <svg class="link-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            Home
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('books.index') }}" class="link-item">
                            <div class="link-underline"></div>
                            <svg class="link-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            Browse Books
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('categories.index') }}" class="link-item">
                            <div class="link-underline"></div>
                            <svg class="link-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                            Categories
                        </a>
                    </li>
                    
                    
                </ul>
            </div>

            <!-- Contact & Policies -->
            <div>
                <h3 class="section-title">
                    <svg class="section-title-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    Contact Us
                </h3>
                <ul class="contact-list">
                    <li class="contact-item">
                        <div class="contact-icon-wrapper">
                            <svg class="contact-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <span class="contact-label">Email</span>
                            <a href="mailto:support@pageturner.com" class="contact-link">
                                support@pageturner.com
                            </a>
                        </div>
                    </li>
                    <li class="contact-item">
                        <div class="contact-icon-wrapper">
                            <svg class="contact-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                        </div>
                        <div>
                            <span class="contact-label">Phone</span>
                            <a href="tel:+1234567890" class="contact-link">
                                (123) 456-7890
                            </a>
                        </div>
                    </li>
                    <li class="contact-item">
                        <div class="contact-icon-wrapper">
                            <svg class="contact-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <span class="contact-label">Address</span>
                            <span class="contact-address">123 Book Street,<br>Literary City, LC 12345</span>
                        </div>
                    </li>
                </ul>
            </div>
        </div>

        

        <!-- Policies & Copyright -->
        <div class="policies-section">
            <div class="policies-container">
                <div class="copyright">
                    <p>&copy; {{ date('Y') }} PageTurner Bookstore. All rights reserved.</p>
                    <p class="copyright-heart">
                        <svg class="heart-icon" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
                        </svg>
                        Designed with passion for book lovers everywhere.
                    </p>
                </div>
                
                <div class="policy-links">
                    <a  class="policy-link">Privacy Policy</a>
                    <a  class="policy-link">Terms of Service</a>
                    <a  class="policy-link">Shipping Policy</a>
                    <a  class="policy-link">Return Policy</a>
                    <a  class="policy-link">Cookie Policy</a>
                </div>
            </div>
        </div>

        <!-- Reading Quote -->
        <div class="quote-section">
            <p class="quote-text">
                "A room without books is like a body without a soul." – Cicero
            </p>
        </div>
    </div>
</footer>

