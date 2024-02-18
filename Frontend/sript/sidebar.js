const hamburger = document.querySelector(".hamburger");
const content = document.querySelector(".content");
const sidebar = document.querySelector(".sidebar");
const logoutBtn = document.querySelector("#logout");

let sidebarOpen = true;
let small = false;

window.addEventListener("DOMContentLoaded", async () => {
  hamburger.addEventListener("click", toggleSideBar);

  logoutBtn.addEventListener("click", (e) => {
    e.preventDefault();

    fetch("../../../Backend/controller/logout.php");
    window.location.replace(
      "http://localhost/Client/Frontend/views/index.html"
    );
  });
});

function toggleSideBar() {
  if (sidebarOpen) {
    content.style.display = "none";
    sidebar.style.width = "auto";
    sidebarOpen = false;
  } else {
    content.style.display = "unset";
    sidebar.style.width = "20%";
    sidebarOpen = true;
  }
}
