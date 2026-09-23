window.richEditors = {};

document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('[data-rich-editor]').forEach(function (container) {
    var targetId = container.getAttribute('data-rich-editor');
    var textarea = document.getElementById(targetId);
    if (!textarea || typeof Quill === 'undefined') {
      return;
    }

    var editorDiv = document.createElement('div');
    editorDiv.style.minHeight = '260px';
    editorDiv.style.background = '#fff';
    container.appendChild(editorDiv);

    var quill = new Quill(editorDiv, {
      theme: 'snow',
      modules: {
        toolbar: {
          container: [
            [{ header: [2, 3, false] }],
            ['bold', 'italic', 'underline', 'strike'],
            ['blockquote', 'code-block'],
            [{ list: 'ordered' }, { list: 'bullet' }],
            [{ align: [] }],
            ['link', 'image'],
            ['clean'],
          ],
          handlers: {
            image: function () {
              selectAndUploadImage(quill);
            },
          },
        },
      },
    });

    quill.root.innerHTML = textarea.value || '';
    window.richEditors[targetId] = quill;

    var form = textarea.closest('form');
    if (form) {
      form.addEventListener('submit', function () {
        textarea.value = quill.getText().trim() === '' ? '' : quill.root.innerHTML;
      });
    }
  });
});

/**
 * Programmatically replace a rich editor's content (used by the job form's
 * company-select autofill).
 */
window.refreshRichEditor = function (targetId, html) {
  var quill = window.richEditors[targetId];
  if (quill) {
    quill.root.innerHTML = html || '';
  }
};

function selectAndUploadImage(quill) {
  var input = document.createElement('input');
  input.setAttribute('type', 'file');
  input.setAttribute('accept', 'image/png,image/jpeg,image/webp');
  input.click();

  input.onchange = function () {
    var file = input.files[0];
    if (!file) {
      return;
    }

    var range = quill.getSelection(true);
    var formData = new FormData();
    formData.append('file', file);
    formData.append('_csrf', document.querySelector('meta[name="csrf-token"]').content);

    fetch(window.EDITOR_UPLOAD_URL, { method: 'POST', body: formData })
      .then(function (res) { return res.json(); })
      .then(function (data) {
        if (data.url) {
          quill.insertEmbed(range.index, 'image', data.url, 'user');
          quill.setSelection(range.index + 1);
        } else {
          alert(data.error || 'Image upload failed.');
        }
      })
      .catch(function () {
        alert('Image upload failed.');
      });
  };
}
