/**
 * Represents a species with dietary information
 */
export interface Species {
  id: number;
  name: string;
  diet: string; // e.g., "carnivorous"
  clearance: number;
}

/**
 * Represents an enclosure in the zoo
 */
export interface Enclosure {
  id: number;
  name: string;
  size: number;
  positionX: number;
  positionY: number;
  clearance: number;
  animals: Animal[];
}

/**
 * Represents an animal in the zoo
 */
export interface Animal {
  id: number;
  uuid: string;
  name: string;
  age: number;
  gender: boolean;
  size: number;
  weight: number;
  enclosureId: number;
  speciesId: number;
  species: Species;
  enclosure: Enclosure;
}
