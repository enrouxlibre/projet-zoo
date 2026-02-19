export function login(email: string, password: string) {
  const url = import.meta.env.VITE_API_URL;
  console.log(url);
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
      console.log("Success:", data);
      return data;
    })
    .catch((error) => {
      console.error("Error:", error);
      throw error;
    });
}

export function logout() {
  const url = import.meta.env.VITE_API_URL;
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
    .then((data) => {
      console.log("Success:", data);
      localStorage.removeItem("csrfToken");
    })
    .catch((error) => {
      console.error("Error:", error);
    });
}
