// Flag to prevent quick multi-click glitches
let animating = false;

document.querySelectorAll(".next").forEach((button) => {
  button.addEventListener("click", function () {
    if (animating) return false;
    animating = true;

    const current_fs = this.parentNode;
    const next_fs = current_fs.nextElementSibling;

    // Activate next step on progressbar using the index of next_fs
    const progressbarLi = document.querySelectorAll("#progressbar li");
    progressbarLi[
      Array.from(document.querySelectorAll("fieldset")).indexOf(next_fs)
    ].classList.add("active");

    // Show the next fieldset
    next_fs.style.display = "block";

    // Animation
    let opacity = 0;
    let scale = 0.8;
    const animate = () => {
      opacity += 0.04;
      scale += 0.004;
      if (opacity >= 1) {
        current_fs.style.display = "none";
        next_fs.style.opacity = 1;
        animating = false;
        cancelAnimationFrame(animationId);
        return;
      }
      current_fs.style.transform = `scale(${scale})`;
      current_fs.style.opacity = 1 - opacity;
      next_fs.style.opacity = opacity;
      requestAnimationFrame(animate);
    };
    let animationId = requestAnimationFrame(animate);
  });
});

document.querySelectorAll(".previous").forEach((button) => {
  button.addEventListener("click", function () {
    if (animating) return false;
    animating = true;

    const current_fs = this.parentNode;
    const previous_fs = current_fs.previousElementSibling;

    // De-activate current step on progressbar
    const progressbarLi = document.querySelectorAll("#progressbar li");
    progressbarLi[
      Array.from(document.querySelectorAll("fieldset")).indexOf(current_fs)
    ].classList.remove("active");

    // Show the previous fieldset
    previous_fs.style.display = "block";

    // Animation
    let opacity = 0;
    let scale = 1.2;
    const animate = () => {
      opacity += 0.04;
      scale -= 0.004;
      if (opacity >= 1) {
        current_fs.style.display = "none";
        previous_fs.style.opacity = 1;
        animating = false;
        cancelAnimationFrame(animationId);
        return;
      }
      current_fs.style.opacity = 1 - opacity;
      previous_fs.style.transform = `scale(${scale})`;
      previous_fs.style.opacity = opacity;
      requestAnimationFrame(animate);
    };
    let animationId = requestAnimationFrame(animate);
  });
});
