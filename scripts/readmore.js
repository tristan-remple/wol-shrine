(function(){

    function readMore(e){
        const postId = e.target.id;
        const postNum = postId.substr(7);
        if (e.button === 0 || e.keyCode === 13 || e.keyCode === 32) {
            if (e.target.innerHTML === "Read More...") {
                document.getElementById(postNum).classList.remove("hidden");
                e.target.innerHTML = "Read Less";
            } else {
                document.getElementById(postNum).classList.add("hidden");
                e.target.innerHTML = "Read More...";
            }
        }
    }

    const items = document.getElementsByClassName("read-more");
    for (let item of items){
        item.addEventListener("click", function(e){
            readMore(e)
        });
        item.addEventListener("keydown", function(e){
            readMore(e)
        });
    }

})();