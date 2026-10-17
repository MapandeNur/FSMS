import { useEffect, useState } from "react";
import { Alert, Card, Spin, Table, Tag, Typography } from "antd";
import { getPlacements } from "../api/placements";

const { Title } = Typography;

function Placements() {
  const [placements, setPlacements] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");

  useEffect(() => {
    let active = true;

    async function loadPlacements() {
        try {
          setLoading(true);
          setError("");
      
          const result = await getPlacements();
          
          const payload = result && result.data;
      
        
          const rows = Array.isArray(payload)
            ? payload
            : (payload && Array.isArray(payload.data))
              ? payload.data
              : [];
      
          if (active) {
            setPlacements(rows);
          }
        } catch (err) {
          if (active) {
        
            const errorMessage = err.response && err.response.data && err.response.data.message;
            setError(
              errorMessage || "Failed to load placements. Please check the API connection."
            );
          }
        } finally {
          if (active) {
            setLoading(false);
          }
        }
      }
      

    loadPlacements();

    return () => {
      active = false;
    };
  }, []);

  const columns = [
    {
      title: "Registration Number",
      key: "registration_number",
      render: (_, record) => 
        (record && record.student && record.student.registration_number) || "-",
    },
    {
      title: "Student",
      key: "student",
      render: (_, record) => 
        (record && record.student && record.student.name) || "-",
    },
    {
      title: "Department",
      key: "department",
      render: (_, record) => 
        (record && record.department && record.department.name) || "-",
    },
    {
      title: "Supervisor",
      key: "supervisor",
      render: (_, record) => 
        (record && record.supervisor && record.supervisor.name) || "-",
    },
  ];
  [
    {
      title: "Start Date",
      dataIndex: "start_date",
      key: "start_date"
    },
    {
      title: "End Date",
      dataIndex: "end_date",
      key: "end_date"
    },
    {
      title: "Status",
      dataIndex: "status",
      key: "status",
      render: (value) => (
        <Tag color={value === "active" ? "green" : "default"}>
          {value || "Unknown"}
        </Tag>
      )
    }
  ]
  
  return (
    <Card>
      <Title level={2}>Field Placements</Title>

      {error && (
        <Alert
          type="error"
          showIcon
          message={error}
          style={{ marginBottom: 16 }}
        />
      )}

      <Spin spinning={loading}>
        <Table
          rowKey="id"
          columns={columns}
          dataSource={placements}
          pagination={{ pageSize: 10 }}
          locale={{ emptyText: "No placements found." }}
          scroll={{ x: true }}
        />
      </Spin>
    </Card>
  );
}

export default Placements;