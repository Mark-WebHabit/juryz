const BASE_LOCATION = "http://localhost/Client/Frontend/views";
const UPLOADS_LOCATION = "http://localhost/Client/Frontend/";
const form = document.querySelector("#msform");
const back = document.querySelector(".back");
const addBook = document.querySelector(".add-book");
const modalContainer = document.querySelector(".modal-container");
const addTransaction = document.querySelector("#addtransaction-btn");
const tbody = document.querySelector("#tbody");
const noTransaction = document.querySelector("#no-transaction");
const modalView = document.querySelector(".modal-view");
const logoutButton = document.querySelector(".logout");
const filterValue = document.querySelector("#filterValue");
const dropdown = document.querySelector(".dropdown-menu");
const li = dropdown.querySelectorAll("li");

let filter = "All";

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

  // get all the transaction of authenticated user
  await getAllTransactions();

  li.forEach((el) => {
    el.addEventListener("click", async () => {
      clearTbodyContent();
      filterValue.textContent = el.getAttribute("class");
      filter = el.dataset.name;
      await getAllTransactions(el.dataset.name);
    });
  });

  back.addEventListener("click", () => {
    form.reset();
    modalContainer.style.display = "none";
  });

  addBook.addEventListener("click", () => {
    modalContainer.style.display = "grid";
  });

  addTransaction.addEventListener("click", (e) => {
    e.preventDefault();

    fetch("../../../Backend/controller/addtransaction.php", {
      method: "POST",
      body: new FormData(form),
    })
      .then((response) => {
        if (!response.ok) {
          throw new Error(
            "Network response was not ok: " + response.statusText
          );
        }
        return response.json(); // This returns a promise
      })
      .then(async (data) => {
        if (data.success) {
          window.location.reload();
          back.click();
        } else {
          alert(data.message);
        }
      })
      .catch((error) => {
        console.error("Error during fetch:", error);
      });
  });

  logoutButton.addEventListener("click", () => {
    fetch("../../../Backend/controller/logout.php");
    window.location.replace(
      "http://localhost/Client/Frontend/views/index.html"
    );
  });
});

async function getAllTransactions(filter = null) {
  let url;

  if (filter && filter !== "All") {
    url = `../../../Backend/controller/getalltransactions.php?filter=${filter}`;
  } else {
    url = "../../../Backend/controller/getalltransactions.php";
  }
  fetch(url, {
    method: "GET",
  })
    .then((data) => data.json())
    .then((data) => {
      if (data.success) {
        if (!data.data.length) {
          noTransaction.style.display = "block";
        } else {
          noTransaction.style.display = "none";

          data.data.forEach((val) => {
            const tr = document.createElement("tr");

            const sender = document.createElement("td");
            sender.textContent = val.sender_email;
            tr.appendChild(sender);

            const reciever = document.createElement("td");
            reciever.textContent = val?.reciever_email
              ? val.reciever_email
              : val.reciever_name;
            tr.appendChild(reciever);

            let dateBooked = document.createElement("td");
            dateObj = new Date(val.pickup_schedule);
            const options = { year: "numeric", month: "long", day: "numeric" };
            const formattedDate = dateObj.toLocaleDateString("en-US", options);
            dateBooked.textContent = formattedDate;
            tr.appendChild(dateBooked);

            let shippedDate = document.createElement("td");
            if (val.status !== "delivered") {
              shippedDate.textContent = "N/A";
            } else {
              let dateShipped = new Date(val.shipped_date);
              const options = {
                year: "numeric",
                month: "long",
                day: "numeric",
              };
              const formattedDate = dateShipped.toLocaleDateString(
                "en-US",
                options
              );
              shippedDate.textContent = formattedDate;
            }
            tr.appendChild(shippedDate);

            let status = document.createElement("td");
            status.textContent = val.status;
            tr.appendChild(status);

            const action = document.createElement("td");
            const img = document.createElement("img");
            img.src = "../../assets/view.png";
            img.alt = "View";
            img.setAttribute("data-name", val.id);
            img.classList.add("action");
            action.appendChild(img);
            tr.appendChild(action);

            tbody.appendChild(tr);
          });
        }
      }
    })
    .then(() => {
      const actionImages = document.querySelectorAll(".action");
      actionImages.forEach((btn) => {
        btn.addEventListener("click", (e) => {
          // modalView.style.display = "grid";

          fetch(
            `../../../Backend/controller/gettransaction.php?id=${e.target.dataset.name}}`,
            {
              method: "GET",
            }
          )
            .then((response) => response.json())
            .then((data) => {
              const {
                id,
                contact_person,
                delivery_proof,
                dropoff_address,
                item_list,
                package_quantity,
                package_size,
                package_weight,
                pickup_schedule,
                reciever_email,
                reciever_name,
                sender_address,
                sender_email,
                sender_note,
                shipped_date,
                status,
              } = data.data;

              const statusText = document.querySelector(".status");
              const dateBooked = document.querySelector(".date-booked");
              const dateDelivered = document.querySelector(".date-delivered");
              const senderEmail = document.querySelector(".sender-email");
              const pickup = document.querySelector(".pickup-location");
              const dropoff = document.querySelector(".dropoff-location");
              const recieverName = document.querySelector(".reciever-name");
              const recieverEmail = document.querySelector(".reciever-email");
              const weight = document.querySelector(".package-weight");
              const qty = document.querySelector(".package-qty");
              const size = document.querySelector(".package-size");
              const list = document.querySelector(".package-list");
              const note = document.querySelector(".package-note");
              const contact = document.querySelector(".contact-person");
              const img = document.querySelector(".delivery-image");
              const close = document.querySelector(".close-btn");
              const cancel = document.getElementById("cancel");

              if (status == "pending") {
                cancel.style.display = "inline";

                cancel.addEventListener("click", () => {
                  fetch(
                    `../../../Backend/controller/canceltransaction.php?id=${id}`
                  )
                    .then((resposne) => resposne.json())
                    .then(async (data) => {
                      clearTbodyContent();

                      await getAllTransactions(filter);
                      modalView.style.display = "none";
                    })
                    .catch((error) => console.error(error));
                });
              } else {
                cancel.style.display = "none";
              }

              statusText.textContent = status;
              dateBooked.textContent = `Date Booked: ${pickup_schedule}`;
              dateDelivered.textContent = `Date Delivered: ${shipped_date}`;
              senderEmail.textContent = `Email: ${sender_email}`;
              pickup.textContent = `Pickup Location: ${sender_address}`;
              dropoff.textContent = `Dropoff Location: ${dropoff_address}`;
              recieverName.textContent = `name: ${reciever_name}`;
              recieverEmail.textContent = `Email: ${reciever_email || "N/A"}`;
              weight.textContent = `Weight (KG): ${package_weight}`;
              qty.textContent = `Quantity: ${package_quantity}`;
              size.textContent = `Size: ${package_size}`;
              list.textContent = `List: ${item_list || "N/A"}`;
              note.textContent = `Note: ${sender_note || "N/A"}`;
              contact.textContent = `Contact: ${contact_person}`;
              const relativePath =
                delivery_proof &&
                delivery_proof
                  .replace(/\\/g, "/")
                  .split("/Client/Frontend/")[1];

              if (relativePath) {
                img.src = `${UPLOADS_LOCATION}/${relativePath}`;
              } else {
                img.src = "../../assets/empty.png"; // Fallback image if the path isn't correctly parsed
              }
              modalView.style.display = "grid";

              close.addEventListener("click", () => {
                modalView.style.display = "none";
              });
            })
            .catch((error) => console.error("Error:", error));
        });
      });
    })
    .catch((error) => console.log(error.message));
}

function clearTbodyContent() {
  tbody.innerHTML = "";
}
