const tbody = document.querySelector("tbody");
const addVehicleBtn = document.querySelector(".add-vehicle");
const modal = document.querySelector(".modal-add");
const cancel = document.querySelector(".cancel");
const cancelAssign = document.querySelector(".cancel-assign");
const form = document.querySelector(".msform1");
const form2 = document.querySelector(".msform2");
const submit = document.querySelector(".submit");
const submitUpdate = document.querySelector(".submit-update");
const modalAssign = document.querySelector(".modal-assign");
const select = document.querySelector(".select");
const hiddenId = document.querySelector("#selectedVehice");
const plateInput = document.getElementById("vehicle_plate");
const unitInput = document.getElementById("vehicle_unit");

window.addEventListener("DOMContentLoaded", async () => {
  await getVehicles();

  addVehicleBtn.addEventListener("click", () => {
    modal.style.display = "grid";
  });

  cancel.addEventListener("click", () => {
    form.reset();
    modal.style.display = "none";
  });
  cancelAssign.addEventListener("click", () => {
    hiddenId.value = "";
    plateInput.value = "";
    unitInput.value = "";
    form2.reset();
    modalAssign.style.display = "none";
  });

  submit.addEventListener("click", async (e) => {
    await addVehicle(e);
  });

  submitUpdate.addEventListener("click", (e) => {
    e.preventDefault();

    fetch("../../../Backend/controller/updatevehicle.php", {
      method: "POST",
      body: new FormData(form2),
    })
      .then((response) => response.json())
      .then((data) => {
        submitUpdate.click();
        window.location.reload();
      })
      .catch((error) => console.error(error));
  });
});

async function getVehicles() {
  fetch("../../../Backend/controller/getvehicles.php", {
    method: "GET",
  })
    .then((response) => response.json())
    .then((data) => {
      data.data.forEach((val) => {
        const tr = document.createElement("tr");

        const unit = document.createElement("td");
        unit.textContent = val.vehicle_unit;

        const plate = document.createElement("td");
        plate.textContent = val.vehicle_plate;

        const driver = document.createElement("td");
        driver.textContent = val?.drivername ? val.drivername : "N/A";

        const status = document.createElement("td");
        status.textContent = val.status;

        const action = document.createElement("td");

        const edit = document.createElement("img");
        edit.src = "../../assets/edit.png";
        action.appendChild(edit);

        const trash = document.createElement("img");
        trash.src = "../../assets/trash.png";
        trash.classList.add("mx-3");
        action.appendChild(trash);

        tr.appendChild(unit);
        tr.appendChild(plate);
        tr.appendChild(driver);
        tr.appendChild(status);
        tr.appendChild(action);

        tbody.appendChild(tr);

        edit.addEventListener("click", async () => {
          modalAssign.style.display = "grid";
          await getAvailableDrivers();
          hiddenId.value = val.id;
          plateInput.value = val.vehicle_plate;
          unitInput.value = val.vehicle_unit;
        });

        trash.addEventListener("click", async (e) => {
          await deleteVehicle(e, val.id);
        });
      });
    })
    .catch((error) => console.error(error));
}

async function addVehicle(e) {
  e.preventDefault();
  fetch("../../../Backend/controller/addvehicle.php", {
    method: "POST",
    body: new FormData(form),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        cancel.click();
        window.location.reload();
      }
    })
    .catch((errro) => console.error(errro));
}

async function deleteVehicle(e, id) {
  e.preventDefault();
  fetch(`../../../Backend/controller/deletevehicle.php?id=${id}`, {
    method: "GET",
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        cancel.click();
        window.location.reload();
      }
    })
    .catch((errro) => console.error(errro));
}

async function getAvailableDrivers() {
  // Clear existing options from the select element
  while (select.firstChild) {
    select.removeChild(select.firstChild);
  }

  // Optionally, add a default option like "Select a driver" if needed
  const defaultOption = document.createElement("option");
  defaultOption.textContent = "Select a driver";
  defaultOption.value = "";
  select.appendChild(defaultOption);

  // Fetch and append new options
  fetch("../../../Backend/controller/getavailabledriver.php", {
    method: "GET",
  })
    .then((response) => response.json())
    .then((data) => {
      data.data.forEach((val) => {
        const option = document.createElement("option");
        option.classList.add("text", "p-2", "text-dark");
        option.value = val.id;
        option.textContent = `${val.fname} ${val.lname} || ${val.email}`;

        select.appendChild(option);
      });
    })
    .catch((error) => console.error(error));
}
