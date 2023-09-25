// Create a new instance of the Renderer class
const renderer = new marked.Renderer();

// Override the image method
// Override the image method
renderer.image = function(href, title, text) {
    console.log('renderer.image function invoked'); // Debug line
    console.log("Alt text:", text);  // Debug line
    
    let dimensions = '';
    const dimensionMatch = text.match(/{(.+?)}/);
    console.log('dimensionMatch:', dimensionMatch); // Debug line
    
    if (dimensionMatch) {
    const dimensionString = dimensionMatch[1];
    const dimensionTokens = dimensionString.split(";");
    dimensions = dimensionTokens.map(token => token.replace("=", ":").trim()).join(";");
    text = text.replace(dimensionMatch[0], '').trim();  // Remove dimensions from alt text
    }
    let html = `<img src="${href}" alt="${text}" title="${title}">`;
    if (dimensions) {
    html = html.replace('<img', `<img style="${dimensions}"`);
    }
    console.log('Final HTML:', html); // Debug line
    return html;
};

// Configure marked to use the custom renderer
marked.use({ renderer });

function updatePreviewAndMathJax(inputElementId, outputElementId) {
    const inputElement = document.getElementById(inputElementId);
    const outputElement = document.getElementById(outputElementId);

    // Use the custom renderer
    const htmlContent = marked.parse(inputElement.value);
    outputElement.innerHTML = htmlContent;
    MathJax.Hub.Queue(["Typeset", MathJax.Hub, outputElementId]);
}

// ... (rest of your existing code) ...
document.addEventListener('DOMContentLoaded', function () {
    var contentArea = document.getElementById('content');
    var titleArea = document.getElementById('title');

    // Listen for changes
    contentArea.addEventListener('input', function() { 
        updatePreviewAndMathJax('content', 'contentPreview'); 
    });

    titleArea.addEventListener('input', function() { 
        updatePreviewAndMathJax('title', 'titlePreview'); 
    });

    // Trigger initial rendering
    updatePreviewAndMathJax('content', 'contentPreview');
    updatePreviewAndMathJax('title', 'titlePreview');
});
