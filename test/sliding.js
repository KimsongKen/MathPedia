const images = ["image1.jpg", "image2.jpg"];
let currentIndex = 0;

function changeImage() {
    const imgElement = document.getElementById("slide-image");
    currentIndex = (currentIndex + 1) % images.length;
    console.log("Changing image to " + images[currentIndex]);
    imgElement.src = images[currentIndex];
}

setInterval(changeImage, 3000);
