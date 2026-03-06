import "./Table.scss";
import { getData } from "../../lib/common.ts";
import { useEffect, useState } from "react";
function Table() {
  const [animals, setAnimals] = useState<any[]>([]);
  const [enclosures, setEnclosures] = useState<any[]>([]);

  useEffect(() => {
    const fetchData = async () => {
      const data = await getData("animals");
      setAnimals(data);
    };
    fetchData();
  }, []);

  useEffect(() => {
    const fetchData = async () => {
      const data = await getData("enclosures");
      setEnclosures(data);
    };
    fetchData();
  }, []);

  return (
    <>
      {console.log(animals)}
      {console.log(enclosures)}
    </>
  );
}
export default Table;
