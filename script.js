document.addEventListener('DOMContentLoaded', () => {
    const plusContainer = document.querySelector('.pluscontainer');

    plusContainer.addEventListener('click', function() {
        // Create a new ventureshape
        const newVentureShape = document.createElement('div');
        newVentureShape.classList.add('ventureshape');
        newVentureShape.innerHTML = `
            <h1 class="venturetitle">New Task</h1>
            <span class="gg-color-picker"></span>
            `;

        // Create a new line-frame
        const newLineFrame = document.createElement('div');
        newLineFrame.classList.add('line-frame');

        // Get the parent element of ventureshape2 and pluscontainer
        const parentElement = document.body;

        // Insert the newVentureShape before the pluscontainer
        parentElement.insertBefore(newVentureShape, this);

        // Insert the newLineFrame right after the newVentureShape
        parentElement.insertBefore(newLineFrame, this);
    });
});


