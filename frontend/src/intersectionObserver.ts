console.log("Intersection Observer Loaded");

const observer = new IntersectionObserver((entries) => {
  entries.forEach((entry) => {
    console.log(entry);
    if (entry.isIntersecting) {
      entry.target.animate(
        [
          { opacity: 0, transform: "translateX(-50px)" },
          { opacity: 1, transform: "translateX(0)" },
        ],
        {
          duration: 500,
          easing: "ease-out",
          fill: "forwards",
        },
      );
    }
  });
});

const items = document.querySelectorAll(".ArticleLeft, .ArticleRight");
items.forEach((el) => observer.observe(el));
