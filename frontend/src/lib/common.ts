const url = import.meta.env.VITE_BACKEND_URL || "http://localhost:8000";

export async function getData(endpoint: string) {
  try {
    const response = await fetch(`${url}/api/${endpoint}/`, {
      method: "GET",
      credentials: "include",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": localStorage.getItem("csrfToken") || "",
      },
    });
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    return await response.json();
  } catch (error) {
    console.error(`Error fetching ${endpoint}:`, error);
    throw error;
  }
}

export function postData(endpoint: string, data: any) {
  try {
    return fetch(`${url}/api/${endpoint}/`, {
      method: "POST",
      credentials: "include",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": localStorage.getItem("csrfToken") || "",
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
