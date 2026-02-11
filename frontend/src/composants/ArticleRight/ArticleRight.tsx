import "./ArticleRight.scss";

function ArticleRight({
  img,
  title,
  children,
}: {
  img: string;
  title: string;
  children: React.ReactNode;
}) {
  return (
    <div className="ArticleRight">
      <div className="ArticleRightText">
        <h3>{title}</h3>
        <p>{children}</p>
      </div>
      <img src={img} alt={`image de ${title}`} />
    </div>
  );
}

export default ArticleRight;
