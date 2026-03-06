const url = import.meta.env.VITE_BACKEND_URL || "http://localhost:8000";

export async function getData(endpoint: string) {
  try {
    const response = await fetch(`${url}/api/${endpoint}/`);
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    return await response.json();
  } catch (error) {
    console.error(`Error fetching ${endpoint}:`, error);
    throw error;
  }
}

export function postData(endpoint: string, data: any, X_CSRF: string) {
  try {
    return fetch(`${url}/api/${endpoint}/`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRFToken": X_CSRF,
      },
      body: JSON.stringify(data),
    }).then((response) => {
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      return response.json();
    });
  } catch (error) {
    console.error(`Error posting to ${endpoint}:`, error);
    throw error;
  }
}
