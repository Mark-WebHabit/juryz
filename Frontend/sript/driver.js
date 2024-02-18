const logoutBtn = document.querySelector(".logout");
const authUser = document.querySelector(".auth-user");
const journeyInfo = document.querySelector(".journey-info");
const image = document.querySelector(".journey-image");
const inputFile = document.querySelector("[name=proof]");
const uploadBtn = document.getElementById("upload-btn");
const completeBtn = document.querySelector("#mark-as-completed");
const tbody = document.querySelector("tbody");

let deliveryProof = null;
let transactionId = null;

const BASE_LOCATION = "http://localhost/Client/Frontend/views";

window.addEventListener("DOMContentLoaded", async () => {
  fetch("../../../Backend/middleware/checksession.php", {
    method: "GET",
  })
    .then((data) => data.json())
    .then((data) => {
      if (!data?.success) {
        window.location.replace(`${BASE_LOCATION}/index.html`);
      }
    })
    .catch((error) => console.log(error.message));

  await getauthUSer();
  await getJourneyTransactionWithVehicleAndDriver();
  await getAllCompletedTransactions();

  uploadBtn.addEventListener("click", () => {
    inputFile.click();
  });
  inputFile.addEventListener("change", (e) => {
    const file = e.target.files[0];
    const validExtensions = ["image/jpeg", "image/jpg"];
    const proofImage = document.querySelector(".proof-image"); // Select the image element for preview

    if (file && validExtensions.includes(file.type)) {
      deliveryProof = file; // Storing the file for upload

      // Create a URL for the file and set it as the src for the preview image
      proofImage.src = URL.createObjectURL(file);

      // Optional: Revoke the object URL after the image has been loaded to release memory
      proofImage.onload = () => {
        URL.revokeObjectURL(proofImage.src);
      };

      proofImage.onload = () => {
        URL.revokeObjectURL(proofImage.src); // Clean up the blob URL after loading
      };
    } else {
      alert("Invalid file type. Only JPEG files are allowed.");
      e.target.value = ""; // Reset the file input
      deliveryProof = null;
      proofImage.src = "../../assets/empty.png"; // Reset the preview image to the default/empty state
    }
  });

  completeBtn.addEventListener("click", (e) => {
    if (!deliveryProof || !transactionId) {
      alert("Please select a file and ensure a transaction is selected.");
      return;
    }

    const confirmation = confirm(
      "Are you sure you want to mark the delivery as completed?"
    );

    if (!confirmation) {
      return;
    }

    const formData = new FormData();
    formData.append("transactionId", transactionId);
    formData.append("filePath", deliveryProof.name); // You might not need to explicitly send the file name, as the file object itself contains the name.
    formData.append("file", deliveryProof);

    fetch(
      "../../../Backend/controller/completeTransactionAndUpdateVehicle.php",
      {
        method: "POST",
        body: formData, // Send the FormData object.
      }
    )
      .then((response) => response.json())
      .then((data) => console.log(data))
      .catch((error) => console.error(error.message));
  });
  logoutBtn.addEventListener("click", (e) => {
    e.preventDefault();

    fetch("../../../Backend/controller/logout.php");
    window.location.replace(
      "http://localhost/Client/Frontend/views/index.html"
    );
  });
});

async function getauthUSer() {
  fetch("../../../Backend/controller/getauthuser.php")
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        authUser.textContent = data.data.username;
      }
    })

    .catch((error) => console.error(error));
}

async function getJourneyTransactionWithVehicleAndDriver() {
  fetch(
    "../../../Backend/controller/getJourneyTransactionWithVehicleAndDriver.php"
  )
    .then((response) => response.json())
    .then((data) => {
      if (data.success && data.data) {
        transactionId = data.data.id;
        journeyInfo.style.display = "flex";
        image.style.display = "flex";
        const contactPerson = document.querySelector(
          "[data-name=contact-person]"
        );
        const senderEmail = document.querySelector("[data-name=sender-email]");
        const pickupLocation = document.querySelector(
          "[data-name=pickup-location]"
        );
        const senderNote = document.querySelector("[data-name=sender-note]");
        const recieverName = document.querySelector(
          "[data-name=reciever-name]"
        );
        const recieverEmail = document.querySelector(
          "[data-name=receiver-email]"
        );
        const dropoff = document.querySelector("[data-name=receiver-dropoff]");
        const packageWeight = document.querySelector(
          "[data-name=package-weight]"
        );
        const packageQuantity = document.querySelector(
          "[data-name=package-quantity]"
        );
        const packageSize = document.querySelector("[data-name=package-size]");
        const packageContent = document.querySelector(
          "[data-name=package-content]"
        );
        const transactionStatus = document.querySelector(
          "[data-name=transaction-status]"
        );
        const pickUpSched = document.querySelector(
          "[data-name=transaction-pickup-sched]"
        );
        const shipDate = document.querySelector(
          "[data-name=transaction-ship-date]"
        );

        contactPerson.textContent = data.data.contact_person;
        senderEmail.textContent = data.data.sender_email;
        pickupLocation.textContent = data.data.sender_address;
        senderNote.textContent = data.data.sender_note;
        recieverName.textContent = data.data.reciever_name;
        recieverEmail.textContent = data.data.reciever_email;
        dropoff.textContent = data.data.dropoff_address;
        packageWeight.textContent = `${data.data.package_weight} KG`;
        packageQuantity.textContent = data.data.package_quantity;
        packageSize.textContent = data.data.package_size;
        packageContent.textContent = data.data.item_list;
        transactionStatus.textContent = data.data.status;
        pickUpSched.textContent = data.data.pickup_schedule;
        shipDate.textContent = data.data.shipped_date || "N/A";
      } else {
        journeyInfo.style.display = "none";
        image.style.display = "none";
      }
    })

    .catch((error) => console.error(error));
}

async function getAllCompletedTransactions() {
  fetch("../../../Backend/controller/getcompletedtransactionbydriver.php")
    .then((response) => response.json())
    .then((data) => {
      clearTbody();
      if (data.success && data.data.data.length) {
        data.data.data.forEach((val) => {
          const tr = document.createElement("tr");

          const sender = document.createElement("td");
          sender.textContent = val.sender_email;

          const receiver = document.createElement("td");
          receiver.textContent = val.reciever_name;

          const dateCompleted = document.createElement("td");
          dateCompleted.textContent = val.shipped_date;

          const pickUpAddress = document.createElement("td");
          pickUpAddress.textContent = val.sender_address;

          const dropOffAddress = document.createElement("td");
          dropOffAddress.textContent = val.dropoff_address;

          tr.appendChild(sender);
          tr.appendChild(receiver);
          tr.appendChild(dateCompleted);
          tr.appendChild(pickUpAddress);
          tr.appendChild(dropOffAddress);

          tbody.appendChild(tr);
        });
      } else {
        console.log("mark");
        const h2 = document.createElement("h2");
        h2.setAttribute(
          "class",
          "text-dark position-absolute top-50 start-50 translate-middle"
        );
        h2.textContent = "No Completed Deliveries Yet";
        tbody.appendChild(h2);
      }
    })
    .catch((error) => console.log(error));
}

function clearTbody() {
  tbody.innerHTML = "";
}
