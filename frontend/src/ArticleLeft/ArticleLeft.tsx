import "./ArticleLeft.scss";

function ArticleLeft({
  img,
  title,
  children,
}: {
  img: string;
  title: string;
  children: React.ReactNode;
}) {
  return (
    <div className="ArticleLeft">
      <img src={img} alt={`image de ${title}`} />
      <div className="ArticleLeftText">
        <h3>{title}</h3>
        <p>{children}</p>
      </div>
    </div>
  );
}

export default ArticleLeft;
