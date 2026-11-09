import api, { setAuthToken } from "./api";

export const login = async (credentials) => {
  const response = await api.post("/login", credentials);

  const data = response.data.data;

  localStorage.setItem("token", data.token);
  localStorage.setItem("user", JSON.stringify(data.user));

  setAuthToken(data.token);
  return data;
};

export const logout = () => {
  localStorage.removeItem("token");
  localStorage.removeItem("user");

  setAuthToken(null);
};

export const getUser = () => {
  const user = localStorage.getItem("user");

  return user ? JSON.parse(user) : null;
};

export const getToken = () => localStorage.getItem("token");

export const isAuthenticated = () => {
  return !!getToken();
};

export async function validateToken() {
  const token = getToken();

  if (!token) {
    return false;
  }

  setAuthToken(token);

  try {
    await api.get("/user");
    return true;
  } catch (error) {
    if (error.response?.status === 401) {
      removeToken();
      setAuthToken(null);
    }

    return false;
  }
}
