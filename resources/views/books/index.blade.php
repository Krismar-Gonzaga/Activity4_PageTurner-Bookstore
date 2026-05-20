{{-- resources/views/books/index.blade.php --}}
{{-- Replaces the vanilla book listing with an AI-voice-search-enhanced browse page --}}
@extends('layouts.app')

@section('title', 'Browse Books – PageTurner')

@section('content')
<style>
  :root {
    --pageturner-primary:   #8B4513;
    --pageturner-secondary:  #D2691E;
    --pageturner-accent:     #F4A460;
    --pageturner-light:      #F5EBDC;
    --pageturner-very-light: #FDF8F0;
    --pageturner-dark:       #5D4037;
    --pageturner-text:       #3F2A1D;
    --pageturner-shadow:     0 10px 30px rgba(139,69,19,0.18);
  }

  .page-turner-font { font-family:'Playfair Display',Georgia,serif; }

  /* ── Voice search bar ─────────────────────────────────── */
  .voice-search-bar {
    display:flex; align-items:center; gap:.7rem;
    max-width:640px; margin:0 auto;
    background:#fff; border:2px solid rgba(139,69,19,.15);
    border-radius:999px; padding:.45rem .8rem;
    box-shadow:0 4px 12px rgba(139,69,19,.09);
    transition:all .28s ease;
  }
  .voice-search-bar:focus-within { border-color:var(--pageturner-accent); box-shadow:0 4px 20px rgba(139,69,19,.15); }
  .voice-search-bar input {
    flex:1; border:none; outline:none; background:transparent;
    font-family:'Georgia',serif; font-size:.97rem;
    color:var(--pageturner-text); padding:.35rem 0;
  }
  .voice-search-bar input::placeholder { color:#b0a090; }
  .voice-btn {
    width:40px; height:40px; border-radius:999px; border:none; cursor:pointer;
    background:linear-gradient(135deg,var(--pageturner-primary),var(--pageturner-secondary));
    color:#fff; display:flex; align-items:center; justify-content:center;
    transition:all .3s ease; flex-shrink:0;
  }
  .voice-btn:hover  { transform:scale(1.08); box-shadow:0 4px 14px rgba(139,69,19,.35); }
  .voice-btn.recording { animation:pulse-ring 1.3s ease-in-out infinite; }
  @keyframes pulse-ring { 0%,100%{box-shadow:0 0 0 0 rgba(244,164,96,.7);} 50%{box-shadow:0 0 0 10px rgba(244,164,96,0);} }

  .transcript-chip {
    display:none; max-width:640px; margin:.6rem auto 0;
    background:#fff7ed; border:1px solid #fdba74; border-radius:12px;
    padding:.6rem 1rem; font-size:.88rem; color:#92400e;
  }
  .transcript-chip.visible { display:block; }
  .transcript-chip strong  { font-weight:700;color:#7c2d12; }

  /* ── OpenAPI key warning ───────────────────────────────── */
  .api-config-warn {
    max-width:700px;margin:1rem auto;
    background:#fff7ed;border:1.5px solid #fdba74;border-radius:12px;
    padding:1rem 1.4rem;font-size:.9rem;color:#92400e;text-align:center;
  }

  /* ── Filter bar ────────────────────────────────────────── */
  .browse-filter-bar {
    display:flex;gap:.7rem;flex-wrap:wrap;justify-content:center;
    margin:1.5rem 0;
  }
  .browse-filter-btn {
    padding:.38rem 1.1rem;border-radius:999px;font-size:.85rem;font-weight:600;
    border:1.5px solid rgba(139,69,19,.2);background:#fff;
    color:var(--pageturner-text);cursor:pointer;transition:all .22s ease;
    text-decoration:none;display:inline-block;
  }
  .browse-filter-btn:hover, .browse-filter-btn.active {
    background:var(--pageturner-primary);color:#fff;border-color:var(--pageturner-primary);
  }

  /* ── Grid ──────────────────────────────────────────────── */
  .books-grid {
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(260px,1fr));
    gap:1.6rem;
  }

  /* ── Book card inline for voice results ────────────────── */
  .voice-result-card {
    background:#fff;border-radius:1rem;overflow:hidden;
    transition:var(--pageturner-transition);
    border:1px solid rgba(139,69,19,.12);
    box-shadow:0 10px 30px rgba(139,69,19,.15);
  }
  .voice-result-card:hover { transform:translateY(-6px); }

  .ai-callout {
    background:#ecfdf5;border:1px solid #6ee7b7;border-radius:999px;
    color:#065f46;font-size:.72rem;font-weight:600;
    padding:.22rem .6rem;display:inline-flex;align-items:center;gap:.3rem;
  }

  .section-heading {
    font-size:clamp(1.4rem,2vw+.6rem,2rem);
    font-weight:700;color:var(--pageturner-primary);
    margin-bottom:.3rem;
  }
  .section-sub { font-size:.95rem;color:#6b7280;margin-bottom:1.4rem; }

  @media(max-width:640px){
    .voice-search-bar { border-radius:14px; }
  }
</style>

<div
  x-data="booksBrowse()"
  x-init="init()"
  class="pt-6 md:pt-10"
  role="main"
>

  <!-- ── Voice Search Bar ──────────────────────────────────── -->
  <div style="padding:2.5rem 0 0;">
    <div class="text-center mb-5">
      <h1 class="section-heading page-turner-font">📚 Browse Books</h1>
      <p class="section-sub">Search by voice or type a title, author, or genre below.</p>
    </div>

    <div class="voice-search-bar" role="search" aria-label="Voice book search">
      <svg width="18" height="18" fill="none" stroke="var(--pageturner-secondary)" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
      </svg>
      <input
        type="search"
        placeholder="Type or speak a book title, author, or genre…"
        aria-label="Search books"
        x-model="searchQuery"
        @keydown.enter="performSearch()"
        @input="onTyping()"
        autocomplete="off" spellcheck="false"
      >
      <button type="button"
        class="voice-btn"
        :class="{recording: isRecording}"
        :title="isRecording ? 'Stop' : 'Voice Search'"
        :aria-label="isRecording ? 'Stop recording' : 'Start voice search'"
        @click="toggleRecording()">
        <svg x-show="!isRecording" width="18" height="18" fill="white" viewBox="0 0 24 24">
          <path d="M12 14c1.66 0 2.99-1.34 2.99-3L15 5c0-1.66-1.34-3-3-3S9 3.34 9 5v6c0 1.66 1.34 3 3 3zm5.3-3c0 3-2.54 5.1-5.3 5.1S6.7 14 6.7 11H5c0 3.41 2.72 6.23 6 6.72V21h2v-3.28c3.28-.48 6-3.3 6-6.72h-1.7z"/>
        </svg>
        <svg x-show="isRecording" width="18" height="18" fill="white" viewBox="0 0 24 24">
          <path d="M6 6h12v12H6z"/>
        </svg>
      </button>
    </div>

    <!-- Transcript chip -->
    <div class="transcript-chip" :class="{visible: transcript}" x-show="transcript" role="status">
      🎙 <strong>You said:</strong> <span x-text="transcript"></span>
    </div>

    @if(!$openaiConfigured)
      <div class="api-config-warn">
        ⚠️ <strong>OpenAI API key not yet configured.</strong><br>
        Voice search and AI-written audio descriptions require an OpenAI key.
        <code>OPENAI_API_KEY</code> in your <code>.env</code> unlocks voice search &amp; narration.
        Browse the full book catalogue works without it.
      </div>
    @endif
  </div>

  <!-- ── Category Filters ──────────────────────────────────── -->
  @if($categories->isNotEmpty())
    <div class="browse-filter-bar" role="group" aria-label="Filter by category">
      <a
        class="browse-filter-btn"
        :class="{active:@js(request()->filled('category')?'true':'false')}"
        href="{{ route('books.index') }}">All</a>
      @foreach($categories as $cat)
        <a
          class="browse-filter-btn"
          :class="{active:@js(request('category')==$cat->id?'true':'false')}"
          href="{{ route('books.index', ['category'=>$cat->id]) }}">{{ $cat->name }}</a>
      @endforeach
    </div>
  @endif

  <!-- ── Server-Rendered Paginated Book Grid ────────────────── -->
  <div class="books-grid">
    @forelse($books as $book)
      <x-book-card :book="$book">
        {{-- AI narrated callout (before title) --}}
        <x-slot name="aiCalloutTitleTopSlot">
          <span class="ai-callout">✨ AI narrated</span>
        </x-slot>

        {{-- TTS Listen button after price, before stock info --}}
        <x-slot name="aiTtsAfterPriceSlot">
          <button type="button"
            class="listen-btn"
            onclick="(function(id){const a=document.getElementById('tts-'+id);if(!a.src){a.src='/ai/audio/tts/'+id;}a.play().catch(function(){});})({{ (string)$book->id }});">
            <svg viewBox="0 0 24 24" fill="currentColor" width="14" height="14">
              <path d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.06c1.48-.73 2.5-2.25 2.5-4.03z"/>
            </svg>
            🎧 Listen
          </button>
          <audio id="tts-{{ (string)$book->id }}"></audio>
        </x-slot>

        {{-- Custom action buttons (View Details + Add to Cart) --}}
        <x-slot name="aiCustomActionButtonsSlot">
          <a href="{{ route('books.show', $book) }}" class="view-details-btn">View Details</a>
          @auth
            @if(!auth()->user()->isAdmin() && $book->stock_quantity > 0 && auth()->user()->hasVerifiedEmail())
              <form action="{{ route('cart.add', $book) }}" method="POST" class="flex-1">
                @csrf
                <input type="hidden" name="quantity" value="1">
                <button type="submit" class="add-to-cart-btn">Add to Cart</button>
              </form>
            @endif
          @else
            @if($book->stock_quantity > 0)
              <a href="{{ route('login') }}" class="login-prompt-btn">Login to Buy</a>
            @endif
          @endauth
        </x-slot>
      </x-book-card>
    @empty
      <div class="content-card p-10 text-center" style="grid-column:1/-1;" x-show="!transcript">
        <div style="font-size:3rem;margin-bottom:.8rem;opacity:.7;">📚</div>
        <h3 class="text-xl font-bold" style="color:var(--pageturner-dark);">No books found</h3>
        <p style="color:#6b7280;margin-top:.4rem;">Try a different category or search term.</p>
      </div>
      <div class="content-card p-10 text-center" style="grid-column:1/-1;" x-show="transcript">
        <div style="font-size:2.8rem;margin-bottom:.8rem;opacity:.6;">🔍</div>
        <h3 class="text-xl font-bold" style="color:var(--pageturner-dark);">No books for <em x-text="transcript"></em></h3>
        <p style="color:#6b7280;margin-top:.4rem;">Try saying it a different way, or type to refine your search.</p>
        <button type="button" @click="searchQuery=''; transcript='';"
          style="margin-top:.9rem;padding:.45rem 1.2rem;border-radius:999px;border:1.5px solid var(--pageturner-primary);
                 background:var(--pageturner-light);color:var(--pageturner-primary);font-weight:600;cursor:pointer;">
          Clear &amp; start over
        </button>
      </div>
    @endforelse
  </div>

  <!-- ── Pagination ─────────────────────────────────────────── -->
  @if($books->hasPages())
    <nav aria-label="Pagination" class="flex justify-center gap-2 mt-8">
      @if($books->onFirstPage())
        <span class="browse-filter-btn" style="opacity:.5;cursor:default;">← Prev</span>
      @else
        <a class="browse-filter-btn" href="{{ $books->previousPageUrl() }}">← Prev</a>
      @endif

      <span style="align-self:center;font-size:.9rem;color:#6b7280;padding:0 .5rem;">
        Page {{ $books->currentPage() }} of {{ $books->lastPage() }}
      </span>

      @if($books->hasMorePages())
        <a class="browse-filter-btn" href="{{ $books->nextPageUrl() }}">Next →</a>
      @else
        <span class="browse-filter-btn" style="opacity:.5;cursor:default;">Next →</span>
      @endif
    </nav>
  @endif

  <!-- ── Stats bar ──────────────────────────────────────────── -->
  @if(isset($stats))
    <div style="display:flex;gap:1.2rem;justify-content:center;flex-wrap:wrap;margin-top:2rem;padding-top:1.5rem;border-top:1px solid rgba(139,69,19,.1);">
      <span style="font-size:.82rem;color:#6b7280;">Total books: <strong style="color:var(--pageturner-dark);">{{ number_format($stats['total_books']) }}</strong></span>
      <span style="font-size:.82rem;color:#6b7280;">In stock: <strong style="color:#16a34a;">{{ number_format($stats['total_stock']) }}</strong></span>
      <span style="font-size:.82rem;color:#6b7280;">Low stock: <strong style="color:#ca8a04;">{{ $stats['low_stock_count'] }}</strong></span>
      <span style="font-size:.82rem;color:#6b7280;">Out of stock: <strong style="color:var(--pageturner-error);">{{ $stats['out_of_stock_count'] }}</strong></span>
      <span style="font-size:.82rem;color:#6b7280;">Total value: <strong style="color:var(--pageturner-primary);">₱{{ number_format($stats['total_value'],2) }}</strong></span>
    </div>
  @endif
</div><!-- /x-data -->

<script>
(function(){
  function booksBrowse(){
    return {
      searchQuery  : '',
      transcript   : '',
      isRecording  : false,
      mediaRecorder: null,
      typingTimer  : null,

      init(){}, // Can add inline Alpine announcements here

      onTyping(){
        clearTimeout(this.typingTimer);
        this.typingTimer = setTimeout(() => this.performSearch(), 700);
      },

      async performSearch(){
        const q = this.searchQuery.trim();
        if(!q) return;
        // Navigate to the native search — preserves pagination & filters
        const url = new URL('{{ route("books.index") }}', window.location.href);
        url.searchParams.set('search', q);
        window.location.href = url.toString();
      },

      async toggleRecording(){
        if(this.isRecording){ this.stopRecording(); return; }
        try{
          const stream = await navigator.mediaDevices.getUserMedia({audio:true});
          this.mediaRecorder = new MediaRecorder(stream);
          const chunks=[];
          this.mediaRecorder.ondataavailable = e => chunks.push(e.data);
          this.mediaRecorder.onstop = async () => {
            const blob = new Blob(chunks,{type:'audio/webm'});
            stream.getTracks().forEach(t=>t.stop());
            await this.sendAudio(blob);
          };
          this.mediaRecorder.start();
          this.isRecording = true;
        }catch(err){
          alert('Microphone access was denied. Please allow microphone access and try again.');
          console.error(err);
        }
      },

      stopRecording(){
        if(this.mediaRecorder && this.mediaRecorder.state !== 'inactive'){
          this.mediaRecorder.stop();
        }
        this.isRecording = false;
      },

      async sendAudio(blob){
        const fd  = new FormData();
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
        fd.append('audio_data', blob, 'voice.webm');
        fd.append('filename',   'voice.webm');
        fd.append('limit',      12);
        if(csrf) fd.append('_token', csrf);

        try{
          const r = await fetch('/ai/voice-search',{method:'POST',body:fd,
            headers: csrf ? {'X-CSRF-TOKEN': csrf} : {}
          });
          const ct = r.headers.get('content-type') || '';
          if(!ct.includes('application/json')){
            const txt = await r.text();
            throw new Error(txt.substring(0,200) || 'Unexpected response (HTTP '+r.status+')');
          }
          if(!r.ok) throw new Error((await r.json()).message || 'Search failed (HTTP '+r.status+')');
          const data = await r.json();

          const transcript  = (data.transcript || '').trim();
          const corrected   = (data.corrected_query || transcript).trim();
          const hasResults  = data.results && data.results.length > 0;

          // ── Update search bar & show transcript chip regardless ──────────
          this.transcript  = data.transcript || '';
          this.searchQuery = corrected;          // typed voice fit in search bar
          document.querySelector('.transcript-chip')?.classList.add('visible');

          // ── CASE A — No voice detected (silence / empty audio) ──────────
          if(!transcript){
            // Don't alert — just turn off and stay on browse view
            this.isRecording = false;
            return;
          }

          // ── CASE B — Voice heard but NO matching books ──────────────────
          if(!hasResults){
            // Keep search bar populated so user can edit; show friendly hint
            document.querySelector('.transcript-chip')?.classList.add('visible');
            // Trigger the native text-search so the book grid updates (likely empty)
            const url = new URL('{{ route("books.index") }}', window.location.href);
            url.searchParams.set('search', corrected);
            window.location.href = url.toString();
            return;
          }

          // ── CASE C — Voice heard + results found ────────────────────────
          // Source the AI-corrected query and do the server search
          const url = new URL('{{ route("books.index") }}', window.location.href);
          url.searchParams.set('search', corrected);
          window.location.href = url.toString();

        }catch(e){
          console.error('Voice search error:', e);
          alert('Voice search failed: ' + e.message);
        }finally{
          this.isRecording = false;
        }
      },
    };
  }
  document.addEventListener('alpine:init',()=>{ window.booksBrowse = booksBrowse; });
})();
</script>
@endsection
