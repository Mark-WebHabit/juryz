const tbody = document.querySelector("tbody");
const searchBtn = document.querySelector(".search-btn");
const search = document.querySelector("#search");
const searchOutput = document.querySelector(".search-output");
const addButton = document.querySelector(".add-emp");
const modalAdd = document.querySelector(".modal-add");
const cancelAdd = document.querySelector(".cancel-add");

window.addEventListener("DOMContentLoaded", async () => {
  await getAllEmployee();

  addButton.addEventListener("click", () => {
    modalAdd.style.display = "grid";
  });

  searchBtn.addEventListener("click", () => {
    searchUser(search.value);
  });

  cancelAdd.addEventListener("click", () => {
    search.value = "";
    modalAdd.style.display = "none";
  });
});

async function getAllEmployee() {
  // Fetch and append new options
  fetch("../../../Backend/controller/getalldriver.php", {
    method: "GET",
  })
    .then((response) => response.json())
    .then((data) => {
      data?.data.forEach((val) => {
        const tr = document.createElement("tr");

        const name = document.createElement("td");
        name.textContent = val.fname + " " + val.lname;
        tr.appendChild(name);

        const contact = document.createElement("td");
        contact.textContent = val.contact;
        tr.appendChild(contact);

        const vehicle = document.createElement("td");
        vehicle.textContent = val.vehicle_unit || "N/A";
        tr.appendChild(vehicle);

        const plate = document.createElement("td");
        plate.textContent = val.vehicle_plate || "N/A";
        tr.appendChild(plate);

        const status = document.createElement("td");
        status.textContent = Number(val.status) ? "ONLINE" : "OFFLINE";
        status.classList.add("text");
        if (Number(val.status)) {
          status.classList.add("text-success");
        } else {
          status.classList.add("text-danger");
        }
        tr.appendChild(status);

        const availabilty = document.createElement("td");
        availabilty.textContent = val.vehicle_status || "unassigned";
        tr.appendChild(availabilty);

        const action = document.createElement("td");
        action.textContent = "REMOVE";
        action.classList.add("remove");
        action.classList.add("text-danger");
        tr.appendChild(action);
        tbody.appendChild(tr);

        action.addEventListener("click", () => {
          fetch(`../../../Backend/controller/removeemployee.php?id=${val.id}`, {
            method: "GET",
          })
            .then((response) => response.json())
            .then((data) => window.location.reload())
            .catch((error) => console.error(error));
        });
      });
    })
    .catch((error) => console.error(error));
}

async function searchUser(val) {
  fetch(`../../../Backend/controller/searchusers.php?search=${val}`, {
    method: "GET",
  })
    .then((response) => response.json())
    .then((data) => {
      data?.data.forEach((value) => {
        const result = document.createElement("div");
        result.classList.add("result");

        const name = document.createElement("p");
        name.textContent = value.fname + " " + value.lname;
        name.classList.add("fs-5");
        result.appendChild(name);

        const email = document.createElement("p");
        email.textContent = value.email;
        email.classList.add("fs-5");
        result.appendChild(email);

        searchOutput.appendChild(result);

        result.addEventListener("click", () => {
          fetch(`../../../Backend/controller/updaterole.php?id=${value.id}`, {
            method: "GET",
          })
            .then((response) => response.json())
            .then((data) => {
              cancelAdd.click();
              window.location.reload();
            })
            .catch((error) => console.error(error.message));
        });
      });
    })
    .catch((error) => console.error(error));
}
