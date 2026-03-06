const url = import.meta.env.VITE_API_URL || "http://127.0.0.1:8000";

export function fetchAPI(endpoint: string, method: string, body?: any) {
  try {
    return fetch(`${url}/api/${endpoint}/`, {
      method: method,
      credentials: "include",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": localStorage.getItem("csrfToken") || "",
      },
      body: body ? JSON.stringify(body) : undefined,
    }).then((response) => {
      if (!response.ok) {
        if (response.status === 401) {
          localStorage.removeItem("csrfToken");
          window.location.href = "/login";
          return;
        }
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      return response.json();
    });
  } catch (error) {
    console.error(`Error fetching ${endpoint}:`, error);
    throw error;
  }
}
