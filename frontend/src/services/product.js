import api from "./api";

export const getProducts = (params = {}) => {
  return api.get("/products", { params });
};

export const createProduct = (data) => {
  return api.post("/products", data);
};

export const updateProduct = (id, data) => {
  return api.put(`/products/${id}`, data);
};
