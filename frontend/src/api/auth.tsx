const url = "http://localhost:8000";

// const url = import.meta.env.VITE_API_URL;

export function login(email: string, password: string) {
  return fetch(`${url}/api/login`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      email: email,
      password: password,
    }),
  })
    .then((response) => response.json())
    .then((data) => {
      localStorage.setItem("csrfToken", data.csrfToken);
      return data;
    })
    .catch((error) => {
      throw error;
    });
}

export function logout() {
  const token = localStorage.getItem("csrfToken");
  if (!token) {
    console.error("No CSRF token found. User might not be logged in.");
    return;
  }
  fetch(`${url}/api/logout`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      "X-CSRF-TOKEN": token,
    },
  })
    .then((response) => response.json())
    .then(() => {
      localStorage.removeItem("csrfToken");
    })
    .catch((error) => {
      throw error;
    });
}
