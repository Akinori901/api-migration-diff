import { useOrders } from "../api/orders";

// 旧 API（order/order_list）に合わせて書かれた一覧画面。
export function OrderList() {
  const { orders } = useOrders();
  return (
    <table>
      <tbody>
        {orders.map((row) => (
          <tr key={row.order_no}>
            <td>{row.order_no}</td>
            <td>{row.customer_name}</td>
            <td>{row.total.toLocaleString()}</td>
            <td>{row.delivery_date ?? "-"}</td>
            <td title={row.remarks}>{row.created_at}</td>
          </tr>
        ))}
      </tbody>
    </table>
  );
}
