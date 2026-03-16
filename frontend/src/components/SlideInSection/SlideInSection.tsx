import { useInView } from "react-intersection-observer";
import "./SlideInSection.scss";

function SlideInSection({
  children,
  align = "left",
}: {
  children: React.ReactNode;
  align?: "left" | "right";
}) {
  const { ref, inView } = useInView({
    triggerOnce: true,
    threshold: 0.2,
  });

  return (
    <div
      ref={ref}
      className={`slide-in-section ${align} ${inView ? "visible" : ""}`}
    >
      {children}
    </div>
  );
}

export default SlideInSection;
