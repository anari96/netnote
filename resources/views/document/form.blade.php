<input type="text" placeholder="Keterangan" name="description">
<input type="date" value="{{ date("Y-m-d") }}" name="date">
<button type="submit">Submit</button>

<h2>File</h2>
<button type="button" id="addDetail">Tambah File</button>
<div id="detail">

</div>
<script>
    let detailElement = document.getElementById("detail");
    let addDetailButton = document.querySelector("#addDetail");

    addDetailButton.addEventListener("click", () =>{
        //create element
        formElement = document.createElement("div");
        //change attribute
        formElement.setAttribute("class", "detailForm");

        keteranganInput = document.createElement("input");
        keteranganInput.setAttribute("name", "detailDescription[]");
        keteranganInput.setAttribute("placeholder", "Keterangan");

        fileInput = document.createElement("input")
        fileInput.setAttribute("type", "file");
        fileInput.setAttribute("multiple", "");
        fileInput.setAttribute("name", "detailFile[]");

        fileCountInput = document.createElement("input");
        fileCountInput.setAttribute("name", "detailFileCount[]");
        fileCountInput.classList.add("detailFileCountInput");
        //append to element
        formElement.appendChild(keteranganInput);
        formElement.appendChild(fileInput);
        formElement.appendChild(fileCountInput);
        detailElement.appendChild(formElement);

        addEvent(fileInput);
    });


    function addEvent(element){
        element.addEventListener("input", (event) => {
            let sibling = event.target.nextElementSibling;
            let fileCount = event.target.files.length;
            console.log(fileCount);
            sibling.value = fileCount;
        });
    }
</script>