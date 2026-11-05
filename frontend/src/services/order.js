import api from "./api";

export const getOrders = (params = {}) => {
  return api.get("/orders", { params });
};
