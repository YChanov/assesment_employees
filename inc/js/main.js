function imgError(image) {
    image.onerror = "";
    image.src = "inc/img/emptyProfilePic2.svg";
    return true;
}