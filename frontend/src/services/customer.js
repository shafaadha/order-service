import api from "./api";

export const getCustomers = (params = {}) => {
  return api.get("/customers", { params });
};

export const createCustomer = (data) => {
  return api.post("/customers", data);
};
