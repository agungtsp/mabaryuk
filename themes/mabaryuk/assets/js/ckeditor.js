var editor

ClassicEditor
.create( document.querySelector( '#editor' ), {
    toolbar: ["heading", "|", "bold", "italic", "underline", "link", "bulletedList", "numberedList", "|", "outdent", "indent", "|", "undo", "redo"],
} )
.then( newEditor => {
    editor = newEditor;
} )
.catch( error => {
    console.error( error );
} );
