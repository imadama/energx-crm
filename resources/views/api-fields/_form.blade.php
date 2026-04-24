@php
  $isEdit = isset($apiField);
  $allowedText = $isEdit && $apiField->type === 'list'
    ? implode("\n", $apiField->allowed_values ?? [])
    : old('allowed_values_text', '');
@endphp

<div class="card" style="max-width:760px">
  <div class="card-body">
    <div class="form-grid-2">
      <div class="form-group">
        <label class="form-label">Veldnaam (key) *</label>
        <input class="form-input" type="text" name="key" value="{{ old('key', $apiField->key ?? '') }}" required placeholder="bijv. meters_cable">
        @error('key')<div class="form-error">{{ $message }}</div>@enderror
      </div>

      <div class="form-group">
        <label class="form-label">Type *</label>
        <select class="form-select" name="type" required onchange="toggleAllowedValues(this.value)">
          @foreach(['text'=>'Tekst','integer'=>'Integer','decimal'=>'Decimaal','list'=>'Lijst'] as $val => $lbl)
            <option value="{{ $val }}" {{ old('type', $apiField->type ?? 'text') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
          @endforeach
        </select>
        @error('type')<div class="form-error">{{ $message }}</div>@enderror
      </div>

      <div class="form-group" style="grid-column:1/-1">
        <label class="form-label">Label (optioneel)</label>
        <input class="form-input" type="text" name="label" value="{{ old('label', $apiField->label ?? '') }}" placeholder="bijv. Kabel lengte (m)">
        @error('label')<div class="form-error">{{ $message }}</div>@enderror
      </div>

      <div class="form-group" id="allowed-values-wrap" style="grid-column:1/-1;display:none">
        <label class="form-label">Geldige waarden (1 per regel)</label>
        <textarea class="form-input" name="allowed_values_text" rows="6" placeholder="bijv.\noptie_a\noptie_b\noptie_c">{{ $allowedText }}</textarea>
        @error('allowed_values_text')<div class="form-error">{{ $message }}</div>@enderror
      </div>
    </div>
  </div>
</div>

<script>
  function toggleAllowedValues(type) {
    const wrap = document.getElementById('allowed-values-wrap');
    if (!wrap) return;
    wrap.style.display = type === 'list' ? 'block' : 'none';
  }
  document.addEventListener('DOMContentLoaded', () => {
    toggleAllowedValues(@json(old('type', $apiField->type ?? 'text')));
  });
</script>

