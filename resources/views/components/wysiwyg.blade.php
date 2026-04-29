@props(['name', 'value' => '', 'placeholder' => ''])

<div x-data="{
        value: '{{ addslashes(str_replace(["\r", "\n"], '', $value)) }}',
        quill: null
    }"
    x-init="
        quill = new Quill($refs.editor, {
            theme: 'snow',
            placeholder: '{{ $placeholder }}',
            modules: {
                toolbar: [
                    [{ 'size': ['small', false, 'large', 'huge'] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    [{ 'indent': '-1'}, { 'indent': '+1' }],
                    ['clean']
                ]
            }
        });
        quill.root.innerHTML = value;
        quill.on('text-change', function() {
            value = quill.root.innerHTML;
            $refs.input.value = value;
        });

        // Event listener if a parent wants to reset the editor (e.g. after form submit)
        $watch('value', (val) => {
            if (val === '' && quill.root.innerHTML !== '<p><br></p>') {
                quill.root.innerHTML = '';
            }
        });
    "
    wire:ignore
    class="wysiwyg-wrapper"
>
    <input type="hidden" name="{{ $name }}" x-ref="input" :value="value">
    <div x-ref="editor" style="min-height: 150px; background: #fff; font-family: var(--font-body); font-size: 0.95rem;"></div>
</div>

<style>
.wysiwyg-wrapper .ql-toolbar.ql-snow {
    border-top-left-radius: var(--radius-sm);
    border-top-right-radius: var(--radius-sm);
    border-color: #e5e7eb;
    background: #f9fafb;
    font-family: var(--font-body);
}
.wysiwyg-wrapper .ql-container.ql-snow {
    border-bottom-left-radius: var(--radius-sm);
    border-bottom-right-radius: var(--radius-sm);
    border-color: #e5e7eb;
}
.wysiwyg-wrapper .ql-editor {
    font-family: var(--font-body);
    font-size: 0.95rem;
    line-height: 1.5;
}
.wysiwyg-wrapper .ql-editor:focus {
    border-radius: var(--radius-sm);
}
</style>
