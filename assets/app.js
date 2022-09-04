require('./styles/app.scss');
require('bootstrap');

const CKEConfig = {
    toolbar: [
        { name: 'styles', items: ['Bold', 'Italic', 'Blockquote', 'Strike', 'Link'] }
    ]
};

window.addEventListener('DOMContentLoaded', () => {
    Array.from(document.querySelectorAll('.add-another-collection-widget')).forEach(el => {
        el.addEventListener('click', event => {
            const list = document.querySelector(event.target.getAttribute('data-list-selector'));
            let counter = list.dataset.widgetCounter ||  list.children.length;

            let newWidget = list.getAttribute('data-prototype');
            newWidget = newWidget.replace(/__name__/g, counter);
            counter++;
            list.dataset.widgetCounter = counter;
            const newElem = document.createElement(
                list.getAttribute('data-widget-tags')
            )
            newElem.innerHTML = newWidget;

            const removeButton = document.createElement('div');
            removeButton.innerHTML = '<div class="btn btn-danger">' +
                '-' +
                '</div>';

            newElem.appendChild(removeButton);

            list.appendChild(newElem);

            removeButton.addEventListener('click', () => {
                newElem.remove();
            })

            console.log(counter)

            CKEDITOR.replace(document.getElementsByClassName('content-editor')[
                list.querySelectorAll('.meta-item').length - 1
            ], CKEConfig);
        });
    })

});