/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.scss in this case)
import './styles/app.scss';


function event(){
    const books = document.querySelectorAll(".available");
    for(const book of books){
        book.addEventListener("click", () => {
            const req = new XMLHttpRequest();
            req.open("POST", "/book/take");
            req.send(JSON.stringify({"id": book.id}));
            req.onload = () => {
                const resp = JSON.parse(req.responseText);
                console.log(resp);
                show(resp);
            }
        })
    }
}


function show(data){
    document.body.innerHTML =
        `
            <div id="user">User connected: ${data.client.id}</div>
            <div id="user-books">Books taken:</div>
        `;
    for(const book of data.client.books){
        let userBook = document.createElement("span");
        userBook.innerHTML = "-" + book.title + " ";
        document.body.appendChild(userBook);
    }
    document.body.innerHTML += "<br><a href='/'>Rendre les livres</a>"
    let shelfs = document.createElement("div");
    document.body.appendChild(shelfs);
    shelfs.id = "shelf";
    for(const shelf of data.shelfs){
        let row = document.createElement("div");
        shelfs.appendChild(row);
        row.className = "row";
        for(const category of shelf.categories){
            let categoryDiv = document.createElement("div");
            row.appendChild(categoryDiv);
            categoryDiv.className = "category";
            categoryDiv.innerHTML += `<span class='title'>${category.title}</span>`
            for(const book of category.books){
                if(book.available){
                    categoryDiv.innerHTML += `<div class="book available" id='${book.id}'><h4>${book.title} </h4></div>`;
                }
                else{
                    categoryDiv.innerHTML += `<div class="book" id='${book.id}'></div>`;
                }
            }
        }
    }
    event();
}

event();