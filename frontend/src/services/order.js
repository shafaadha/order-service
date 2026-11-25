import api from "./api";

export const getOrders = (params = {}) => {
  return api.get("/orders", { params });
};

export const createOrder = async (payload) => {
  const response = await api.post("/orders", payload);
  return response.data;
};

export const payOrder = async (id) => {
  const response = await api.patch(`/orders/${id}/pay`);
  return response.data;
};

export const cancelOrder = async (id) => {
  const response = await api.patch(`/orders/${id}/cancel`);
  return response.data;
};

export const detailOrder = async (id) => {
  const response = await api.get(`/orders/${id}`);
  return response.data;
};
