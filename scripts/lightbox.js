(function(){

    // FUNCTIONS

    function showImage(e){
        if (e.button === 0 || e.keyCode === 13 || e.keyCode === 32) {
            let source = e.target.src;
            let altText = e.target.alt;
            document.getElementById("lit-img").src = source;
            document.getElementById("lit-img").alt = altText;
            document.getElementById("lightbox").classList.remove("hidden");
            document.getElementById("close").focus();
        }
    }

    function closeLightbox(e) {
        if (e.button === 0 || e.keyCode === 13 || e.keyCode === 32) {
            document.getElementById("lightbox").classList.add("hidden");
        }
    }

    function trapFocus(e) {
        if (e.keyCode === 9) {
            e.preventDefault();
            document.getElementById("close").focus();
        }
    }

    // LISTENERS

    const items = document.getElementsByClassName("posted-image");
    for (let item of items){
        item.addEventListener("click", function(e){
            showImage(e)
        });
        item.addEventListener("keydown", function(e){
            showImage(e)
        });
    }

    document.getElementById("inner-portrait").addEventListener("click", function(e){
        showImage(e)
    });

    document.getElementById("inner-portrait").addEventListener("keydown", function(e){
        showImage(e)
    });

    document.getElementById("close").addEventListener("click", function(e){ closeLightbox(e); });

    document.getElementById("close").addEventListener("keydown", function(e){ closeLightbox(e); });

    document.getElementById("close").addEventListener("keydown", function(e){ trapFocus(e); });

})();