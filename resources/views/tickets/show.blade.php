<x-layouts.crm title="{{ $ticket->nummer }} - {{ $ticket->titel }}">
  <x-slot:actions>
    <a href="{{ route('tickets.index') }}" class="btn btn-secondary">Terug naar overzicht</a>
  </x-slot:actions>

  <!-- Header -->
  <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:24px;background:#fff;padding:24px;border-radius:12px;border:1px solid #ebebeb">
    <div>
      <div style="display:flex;align-items:center;gap:12px;margin-bottom:8px">
        <h1 style="font-family:var(--font-display);font-size:1.6rem;color:var(--green-800);margin:0">{{ $ticket->titel }}</h1>
        <span class="badge" style="
          {{ $ticket->status === 'open' ? 'background:#fee2e2;color:#dc2626;' : '' }}
          {{ $ticket->status === 'in afwachting' ? 'background:#fef3c7;color:#d97706;' : '' }}
          {{ $ticket->status === 'gesloten' ? 'background:#f3f4f6;color:#6b7280;' : '' }}
        ">{{ ucfirst($ticket->status) }}</span>
      </div>
      <div style="color:#666;font-size:.9rem">
        <strong>{{ $ticket->nummer }}</strong> • Aangemaakt op {{ $ticket->created_at->format('d M Y H:i') }} • 
        <a href="{{ route('klanten.show', $ticket->contactpersoon->klant_id) }}" style="color:var(--green-600);text-decoration:none;font-weight:500">
          {{ $ticket->contactpersoon->klant->naam }} ({{ $ticket->contactpersoon->voornaam }} {{ $ticket->contactpersoon->achternaam }})
        </a>
      </div>
    </div>
    
    <!-- Status wijzigen form -->
    <form method="POST" action="{{ route('tickets.update', $ticket) }}" style="display:flex;align-items:center;gap:8px">
      @csrf @method('PUT')
      <select name="status" class="form-select" style="min-width:140px;padding:6px 12px;height:auto">
        <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>Open</option>
        <option value="in afwachting" {{ $ticket->status === 'in afwachting' ? 'selected' : '' }}>In afwachting</option>
        <option value="gesloten" {{ $ticket->status === 'gesloten' ? 'selected' : '' }}>Gesloten</option>
      </select>
      <button type="submit" class="btn btn-secondary btn-sm">Opslaan</button>
    </form>
  </div>

  <div style="display:grid;grid-template-columns:1fr;gap:24px;max-width:900px;margin:0 auto">
    
    <!-- Thread -->
    <div style="display:flex;flex-direction:column;gap:16px">
      @foreach($ticket->reacties as $reactie)
        @php
          $isKlant = $reactie->type === 'klant';
          $isNotitie = $reactie->type === 'notitie';
          $isIntern = $reactie->type === 'intern';
          
          $align = $isKlant ? 'flex-start' : 'flex-end';
          $bg = $isKlant ? '#fff' : ($isNotitie ? '#fef9c3' : 'var(--green-100)');
          $border = $isKlant ? '1px solid #e5e7eb' : ($isNotitie ? '1px solid #fde047' : '1px solid rgba(45,189,110,.2)');
          $margin = $isKlant ? 'margin-right:48px;' : 'margin-left:48px;';
          $textColor = '#1a1a1a';
        @endphp

        <div style="align-self:{{ $align }};{{ $margin }}width:100%;max-width:800px">
          <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;justify-content:{{ $isKlant ? 'flex-start' : 'flex-end' }}">
            @if($isNotitie)
              <span style="background:#fde047;color:#854d0e;font-size:0.65rem;font-weight:700;padding:2px 6px;border-radius:4px;text-transform:uppercase">Interne Notitie</span>
            @endif
            <span style="font-size:0.8rem;font-weight:600;color:#444">
              {{ $isKlant ? $ticket->contactpersoon->voornaam . ' ' . $ticket->contactpersoon->achternaam : ($reactie->gebruiker->name ?? 'Intern') }}
            </span>
            <span style="color:#aaa;font-size:0.75rem">{{ $reactie->created_at->format('d M H:i') }}</span>
            <span style="color:#888;font-size:0.75rem;display:flex;align-items:center" title="Bron: {{ $reactie->bron }}">
              @if($reactie->bron === 'email') ✉️ 
              @elseif($reactie->bron === 'whatsapp') 💬
              @elseif($reactie->bron === 'telefoon') 📞
              @else 🌐 @endif
            </span>
          </div>

          <div style="background:{{ $bg }};border:{{ $border }};border-radius:12px;padding:16px;color:{{ $textColor }};box-shadow:0 1px 2px rgba(0,0,0,0.02)">
            <div style="font-size:0.95rem;line-height:1.5">{!! $reactie->inhoud !!}</div>

            @if(!empty($reactie->bijlagen) && is_array($reactie->bijlagen))
              <div style="margin-top:12px;padding-top:12px;border-top:1px solid rgba(0,0,0,0.05);display:flex;flex-wrap:wrap;gap:8px">
                @foreach($reactie->bijlagen as $bijlage)
                  <a href="{{ Storage::url($bijlage['pad']) }}" target="_blank" style="display:inline-flex;align-items:center;gap:6px;background:rgba(255,255,255,0.5);border:1px solid rgba(0,0,0,0.1);padding:4px 10px;border-radius:6px;text-decoration:none;font-size:0.8rem;color:#333;font-weight:500">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px"><path d="M21.44 11.05l-9.19 9.19a6 6 0 01-8.49-8.49l9.19-9.19a4 4 0 015.66 5.66l-9.2 9.19a2 2 0 01-2.83-2.83l8.49-8.48"/></svg>
                    {{ $bijlage['naam'] }}
                  </a>
                @endforeach
              </div>
            @endif
          </div>
        </div>
      @endforeach
    </div>

    <!-- Nieuwe reactie vorm -->
    <div x-data="{ tab: 'intern' }" style="margin-top:20px;background:#fff;border-radius:12px;border:1px solid #ebebeb;overflow:hidden">
      <div style="display:flex;border-bottom:1px solid #f0f0f0;background:#fafafa">
        <button @click="tab = 'intern'" :class="tab === 'intern' ? 'border-b-2 border-green-600 text-green-800 bg-white' : 'text-gray-500'" style="padding:12px 20px;font-weight:600;background:transparent;border:none;cursor:pointer;flex:1;border-bottom:2px solid transparent">Beantwoorden (Intern)</button>
        <button @click="tab = 'notitie'" :class="tab === 'notitie' ? 'border-b-2 border-yellow-500 text-yellow-700 bg-white' : 'text-gray-500'" style="padding:12px 20px;font-weight:600;background:transparent;border:none;cursor:pointer;flex:1;border-bottom:2px solid transparent">Interne Notitie</button>
        <button @click="tab = 'klant'" :class="tab === 'klant' ? 'border-b-2 border-gray-600 text-gray-800 bg-white' : 'text-gray-500'" style="padding:12px 20px;font-weight:600;background:transparent;border:none;cursor:pointer;flex:1;border-bottom:2px solid transparent">Klantbericht (handmatig)</button>
      </div>

      <div style="padding:20px">
        <form method="POST" action="{{ route('ticket-reacties.store', $ticket) }}" enctype="multipart/form-data">
          @csrf
          <input type="hidden" name="type" :value="tab">
          
          <div class="form-group" style="display:flex;gap:16px;margin-bottom:12px">
            <div style="flex:1">
              <label class="form-label">Bron / Kanaal</label>
              <select name="bron" class="form-select">
                <option value="email">E-mail</option>
                <option value="telefoon">Telefoon</option>
                <option value="whatsapp">WhatsApp</option>
                <option value="portaal">Klantportaal</option>
              </select>
            </div>
            <div style="flex:1">
              <label class="form-label">Bijlagen toevoegen</label>
              <input type="file" name="bijlagen[]" class="form-input" multiple style="padding:6px;background:#f9fafb">
            </div>
          </div>

          <div class="form-group">
            <x-wysiwyg name="inhoud" placeholder="Typ je bericht of notitie..." />
          </div>

          <div style="display:flex;justify-content:flex-end">
            <button type="submit" class="btn" :class="tab === 'notitie' ? 'btn-secondary' : 'btn-primary'" :style="tab === 'notitie' ? 'background:#fef08a;border-color:#fde047;color:#854d0e' : ''">
              <span x-show="tab === 'intern'">Versturen als Intern Bericht</span>
              <span x-show="tab === 'notitie'">Notitie Opslaan</span>
              <span x-show="tab === 'klant'">Klantbericht toevoegen</span>
            </button>
          </div>
        </form>
      </div>
    </div>
    
  </div>
</x-layouts.crm>
