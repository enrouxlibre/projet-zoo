import "./Table.scss";
import { fetchAPI } from "../../api/common.ts";
import { useEffect, useState } from "react";
import { type Animal, type Enclosure } from "../../types";

function Table() {
  const [enclosures, setEnclosures] = useState<Enclosure[]>([]);

  useEffect(() => {
    const fetchData = async () => {
      const data = await fetchAPI("enclosures", "GET");
      setEnclosures(data);
    };
    fetchData();
  }, []);

  const totalAnimals = enclosures.reduce(
    (acc, enclosure) => acc + (enclosure.animals?.length || 0),
    0,
  );

  return (
    <div className="table-container">
      {enclosures.map((enclosure) => (
        <div key={enclosure.id}>
          <table className="enclosure-table">
            <caption>
              Enclos {enclosure.name} | Niveau de danger : {enclosure.clearance}
            </caption>
            <thead>
              <tr>
                <th>Niveau de danger</th>
                <th>Nom</th>
                <th>Âge</th>
                <th>Sexe</th>
                <th>Taille</th>
                <th>Poids</th>
                <th>Espèce</th>
              </tr>
            </thead>
            <tbody>
              {enclosure.animals.map((animal: Animal) => (
                <tr key={animal.id}>
                  <td>{animal.species.clearance}</td>
                  <td>{animal.name}</td>
                  <td>{animal.age}</td>
                  <td>{animal.gender ? "Mâle" : "Femelle"}</td>
                  <td>{animal.size}</td>
                  <td>{animal.weight}</td>
                  <td>{animal.species.name}</td>
                </tr>
              ))}
            </tbody>
          </table>
          <div className="table-footer">
            <div className="footer-info">
              <div className="info-item">
                <span>
                  Animaux : <strong>{enclosure.animals?.length || 0}</strong>
                </span>
              </div>
            </div>
            <div className="footer-actions">
              <button className="btn-primary">Ajouter un animal</button>
              <button className="btn-secondary">Modifier</button>
            </div>
          </div>
        </div>
      ))}

      <div
        style={{
          marginTop: "40px",
          padding: "20px",
          textAlign: "center",
          color: "var(--secondary-text-color)",
        }}
      >
        <p>
          <strong>Total : {totalAnimals} animaux</strong> dans{" "}
          <strong>{enclosures.length} enclos</strong>
        </p>
      </div>
    </div>
  );
}
export default Table;
