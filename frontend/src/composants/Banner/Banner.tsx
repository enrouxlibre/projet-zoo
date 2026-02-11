import "./Banner.scss";

function Banner({
  title,
  catchLine,
  backgroundImg,
}: {
  title: string;
  catchLine: string;
  backgroundImg: string;
}) {
  return (
    <div
      className="banner"
      style={{ backgroundImage: `url(${backgroundImg})` }}
    >
      <div className="titleBanner">
        <h1>{title}</h1>
        <h2>{catchLine}</h2>
      </div>
    </div>
  );
}

export default Banner;
