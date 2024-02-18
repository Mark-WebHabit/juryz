const buttonContainer = document.querySelector(".buttons");
const buttons = buttonContainer.querySelectorAll("button");
const form = document.querySelector(".form");
const title = document.querySelector(".auth-title");
// const login = document.querySelector("#login")
const register = document.querySelector("#register");
const registerForm = document.querySelector(".register-form");
const employeeForm = document.querySelector(".employee-form");
const loginForm = document.querySelector(".login-form");
const backButton = document.querySelector("#back");
const errorMessage = document.querySelector("[data-name=error]");

const BASE_LOCATION = "http://localhost/Client/Frontend/views";

window.addEventListener("DOMContentLoaded", () => {
  fetch("../../Backend/middleware/checksession.php", {
    method: "GET",
  })
    .then((data) => data.json())
    .then((data) => {
      if (data.success) {
        if (data.role !== "admin") {
          window.location.replace(
            `${BASE_LOCATION}/authenticated_page/dashboard.html`
          );
        } else if (data.role == "driver") {
          window.location.replace(
            `${BASE_LOCATION}/authenticated_page/driver.html`
          );
        }
      } else if (data.role == "client") {
        window.location.replace(
          `${BASE_LOCATION}/authenticated_page/home.html`
        );
      }
    })
    .catch((error) => console.log(error.message));

  backButton.addEventListener("click", () => {
    registerForm.reset();
    errorMessage.textContent = "";
    form.style.right = "-100%";
    registerForm.style.display = "none";
  });
  // show the form by slideing it to left
  buttons.forEach((btn) => {
    btn.addEventListener("click", (e) => {
      const target = e.target.getAttribute("data-name");
      if (target == "login") {
        employeeForm.style.display = "none";
        registerForm.style.display = "none";
        loginForm.style.display = "unset";
        title.textContent = "LOGIN";
        form.style.right = 0;

        document
          .querySelector("#login-button")
          .addEventListener("click", (e) => {
            e.preventDefault();

            fetch("../../Backend/controller/login.php", {
              method: "POST",
              body: new FormData(loginForm),
            })
              .then((response) => {
                if (!response.ok) {
                  throw new Error(
                    "Network response was not ok: " + response.statusText
                  );
                }
                return response.json(); // This returns a promise
              })
              .then((data) => {
                if (data.success) {
                  console.log(data.role);
                  alert("Login Successful");
                  backButton.click();
                  if (data.role == "client") {
                    window.location.replace(
                      `${BASE_LOCATION}/authenticated_page/home.html`
                    );
                  } else if (data.role == "driver") {
                    window.location.replace(
                      `${BASE_LOCATION}/authenticated_page/driver.html`
                    );
                  } else if (data.role == "admin") {
                    window.location.replace(
                      `${BASE_LOCATION}/authenticated_page/dashboard.html`
                    );
                  }
                } else {
                  handleErrorMEssage(data.message);
                }
              })
              .catch((error) => {
                console.error("Error during fetch:", error);
              });
          });
      } else if (target == "register") {
        employeeForm.style.display = "none";
        registerForm.style.display = "unset";
        loginForm.style.display = "none";
        title.textContent = "CREATE ACCOUNT";
        form.style.right = 0;

        document
          .querySelector("#register-button")
          .addEventListener("click", (e) => {
            e.preventDefault(); // Prevent default form submission

            fetch("../../Backend/controller/register.php", {
              method: "POST",
              body: new FormData(registerForm), // Assuming 'form' is correctly referencing your form element
            })
              .then((response) => {
                if (!response.ok) {
                  throw new Error(
                    "Network response was not ok: " + response.statusText
                  );
                }
                return response.json(); // This returns a promise
              })
              .then((data) => {
                if (data.success) {
                  backButton.click();
                } else {
                  handleErrorMEssage(data.message);
                }
              })
              .catch((error) => {
                console.error("Error during fetch:", error);
              });
          });
      } else {
        employeeForm.style.display = "unset";
        registerForm.style.display = "none";
        loginForm.style.display = "none";
        title.textContent = "REGISTER AS EMPLOYEE";
        form.style.right = 0;
      }
    });
  });
});

function handleErrorMEssage(message) {
  errorMessage.style.display = "block";
  errorMessage.textContent = message;
}
