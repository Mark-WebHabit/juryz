const tbody = document.querySelector("tbody");
const modalView = document.querySelector(".modal-view");
const closeView = document.querySelector(".close-view");
const openView = document.querySelector(".open-view");
const modalContainer = document.querySelector(".modal-container");
const searchResult = document.querySelector(".search-results");
const assignBtn = document.querySelector("#assign");

const mode = document.querySelector("#filter");

window.addEventListener("DOMContentLoaded", () => {
  getAllPendingTransactions();

  closeView.addEventListener("click", () => {
    modalView.style.display = "none";
  });
});

mode.addEventListener("change", (e) => {
  const val = e.target.value;

  if (val == "pending") {
    assignBtn.style.display = "block";
    getAllPendingTransactions();
  } else if (val == "journey") {
    assignBtn.style.display = "none";
    getAllJourneyTransactions();
  } else if (val == "completed") {
    assignBtn.style.display = "none";
    getAllCompletedTransactions();
  } else if (val == "cancelled") {
    assignBtn.style.display = "none";
    getAllCancelledTransactions();
  }
});
function getAllPendingTransactions() {
  fetch("../../../Backend/controller/getpendingtransactions.php", {
    method: "GET",
  })
    .then((response) => response.json())
    .then((data) => rendertd(data.data))
    .catch((error) => console.error(error));
}

function getAllJourneyTransactions() {
  fetch("../../../Backend/controller/getjourneytransactions.php", {
    method: "GET",
  })
    .then((response) => response.json())
    .then((data) => rendertd(data.data))
    .catch((error) => console.error(error));
}
function getAllCancelledTransactions() {
  fetch("../../../Backend/controller/getcanceltransactions.php", {
    method: "GET",
  })
    .then((response) => response.json())
    .then((data) => rendertd(data.data))
    .catch((error) => console.error(error));
}
function getAllCompletedTransactions() {
  fetch("../../../Backend/controller/getcompletedtransactions.php", {
    method: "GET",
  })
    .then((response) => response.json())
    .then((data) => rendertd(data.data))
    .catch((error) => console.error(error));
}

function removeAllChildrenOfTbody(body) {
  // As long as tbody has a child node, remove it
  while (body.firstChild) {
    body.removeChild(body.firstChild);
  }
}

function rendertd(arr) {
  removeAllChildrenOfTbody(tbody);
  arr.forEach((val) => {
    const tr = document.createElement("tr");

    const reciever = document.createElement("td");
    reciever.textContent = val.reciever_name;

    const sender = document.createElement("td");
    sender.textContent = val.sender_email;

    const pSched = document.createElement("td");
    pSched.textContent = val.pickup_schedule;

    const vehicle = document.createElement("td");
    vehicle.textContent = val.vehicle_unit || "N/A";

    const status = document.createElement("td");
    status.textContent = val.status;

    const action = document.createElement("td");

    action.textContent = "view";

    tr.appendChild(reciever);
    tr.appendChild(sender);
    tr.appendChild(pSched);
    tr.appendChild(vehicle);
    tr.appendChild(status);
    tr.appendChild(action);

    tbody.appendChild(tr);

    action.addEventListener("click", () => {
      fetch(
        `../../../backend/controller/getalldatatransactionbyid.php?id=${val.id}&filter=${mode.value}`
      )
        .then((response) => response.json())
        .then((data) => {
          console.log(data);
          renderAllData(data.data);
        })
        .catch((error) => console.error(error));
    });
  });
}

function renderAllData(transaction) {
  // Retrieve elements by their IDs
  const statusId = document.getElementById("pending");
  const pickupScheduleId = document.getElementById("pickup");
  const receiverNameId = document.getElementById("reciever-name");
  const receiverEmailId = document.getElementById("reciever-email");
  const dropoffAddressId = document.getElementById("dropoff");
  const weightId = document.getElementById("weight");
  const quantityId = document.getElementById("qty");
  const sizeId = document.getElementById("size");
  const contentsId = document.getElementById("list");
  const senderEmailId = document.getElementById("sender_email");
  const senderAddressId = document.getElementById("sender_address");
  const contactPersonId = document.getElementById("contact-person");
  const specialInstructionsId = document.getElementById("note");
  const vehicleUnitId = document.getElementById("vehicle");
  const vehiclePlateId = document.getElementById("plate");
  const driverNameId = document.getElementById("driver-name");
  const driverEmailId = document.getElementById("driver-email");
  const driverContactId = document.getElementById("driver-contact");
  const vehicleStatusId = document.getElementById("vehicle-status");
  const shippedDateId = document.getElementById("shipped-date");
  const deliveryProofId = document.getElementById("prrof");

  // Update the content of each element
  statusId.textContent = transaction.status;
  pickupScheduleId.textContent = transaction.pickup_schedule;
  receiverNameId.textContent = transaction.reciever_name;
  receiverEmailId.textContent = transaction.reciever_email || "N/A";
  dropoffAddressId.textContent = transaction.dropoff_address;
  weightId.textContent = transaction.package_weight + " kg";
  quantityId.textContent = transaction.package_quantity;
  sizeId.textContent = transaction.package_size;
  contentsId.textContent = transaction.item_list || "Not specified";
  senderEmailId.textContent = transaction.sender_email;
  senderAddressId.textContent = transaction.sender_address;
  contactPersonId.textContent = transaction.contact_person;
  specialInstructionsId.textContent = transaction.sender_note || "None";
  vehicleUnitId.textContent = transaction.vehicle_unit || "N/A";
  vehiclePlateId.textContent = transaction.vehicle_plate || "N/A";
  driverNameId.textContent = transaction.driver_name || "N/A";
  driverEmailId.textContent = transaction.driver_email || "N/A";
  driverContactId.textContent = transaction.driver_contact || "N/A";
  vehicleStatusId.textContent = transaction.vehicle_status || "N/A";
  shippedDateId.textContent = transaction.shipped_date || "N/A";
  deliveryProofId.textContent = transaction.delivery_proof || "N/A";

  assignBtn.setAttribute("data-name", transaction.id);

  // Display the modal
  const modalView = document.querySelector(".modal-view");
  modalView.style.display = "grid";

  // Add event listener to close button
  const closeButton = document.querySelector(".modal-view .close-view");
  closeButton.addEventListener("click", () => {
    modalView.style.display = "none"; // Hide the modal
  });
}

// Example usage:
// Assuming you have fetched the transaction data and stored it in `transactionData`
// renderAllData(transactionData);

// Example JavaScript to handle modal visibility and search functionality
const modal = document.getElementById("vehicleSearchModal");
const btnCancel = document.getElementById("cancelSearch");
const searchField = document.getElementById("vehicleSearchField");

// Example function to show modal
function showModal() {
  modal.style.display = "block";
}

// Close the modal on cancel
btnCancel.addEventListener("click", function () {
  modal.style.display = "none";
});

openView.addEventListener("click", (e) => {
  const transactionId = e.target.dataset.name;
  modalContainer.style.display = "flex";

  fetch("../../../Backend/controller/getstandbyvehicle.php")
    .then((response) => response.json())
    .then((data) => {
      clearSearchResults();

      if (!data.data.length) {
        const h2 = document.createElement("h2");
        h2.textContent = "No Vehicle Available";
        searchResult.appendChild(h2);
      }
      data.data.forEach((val) => {
        const container = document.createElement("div");
        container.classList.add("vehicle-standby");
        container.classList.add("d-flex");
        container.classList.add("lign-items-center");

        const vName = document.createElement("p");
        vName.classList.add("text-dark");
        vName.classList.add("flex-grow-1");
        vName.classList.add("p-2");
        vName.textContent = val.vehicle_unit;
        vName.setAttribute("id", "vehicle-name");

        const vPlate = document.createElement("p");
        vPlate.classList.add("text-dark");
        vPlate.classList.add("flex-grow-1");
        vPlate.classList.add("p-2");
        vPlate.textContent = val.vehicle_plate;
        vPlate.setAttribute("id", "vehicle-plate");

        const vDriver = document.createElement("p");
        vDriver.classList.add("text-dark");
        vDriver.classList.add("flex-grow-1");
        vDriver.classList.add("p-2");
        vDriver.textContent = val.driver_name;
        vDriver.setAttribute("id", "vehicle-driver");

        container.appendChild(vName);
        container.appendChild(vPlate);
        container.appendChild(vDriver);

        searchResult.appendChild(container);

        container.addEventListener("click", () => {
          const data = {
            vehicle: val.vehicle_id,
            transaction: transactionId,
          };
          fetch("../../../Backend/controller/assignvehicletotransaction.php", {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
            },
            body: JSON.stringify(data),
          })
            .then((response) => response.json())
            .then((data) => {
              if (data.success) {
                closeView.click();
                window.location.reload();
              }
            })
            .catch((error) => console.error(error));
        });
      });
    })
    .catch((error) => console.log(error));
});

function clearSearchResults() {
  const vehicleSearchResults = document.getElementById("vehicleSearchResults"); // Assuming 'searchResult' is the ID of your container
  while (vehicleSearchResults.firstChild) {
    vehicleSearchResults.removeChild(vehicleSearchResults.firstChild);
  }
}
