import { useEffect, useState } from "react";
import { Alert, Card, Spin, Table, Tag, Typography } from "antd";
import { getDepartments } from "../api/departments";

const { Title } = Typography;

function Departments() {
  const [departments, setDepartments] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");

  useEffect(() => {
    let active = true;
  
    async function loadDepartments() {
        try {
          setLoading(true);
          setError("");
      
        
          const result = await getDepartments();
          const payload = result && result.data;
      
        
          const rows = Array.isArray(payload)
            ? payload
            : (payload && Array.isArray(payload.data))
              ? payload.data
              : [];
      
          if (active) {
            setDepartments(rows);
          }
        } catch (err) {
          if (active) {
        
            const errorMessage = err.response && err.response.data && err.response.data.message;
            setError(
              errorMessage || "Failed to load departments. Please check the API connection."
            );
          }
        } finally {
          if (active) {
            setLoading(false);
          }
        }
      }
      
  
    loadDepartments();
  
    // Cleanup function sets active to false when component unmounts
    return () => {
      active = false;
    };
  }, []); // Empty dependency array means this runs once on mount
  

  const columns = [
    {
      title: "Department",
      dataIndex: "name",
      key: "name",
    },
    {
      title: "Code",
      dataIndex: "code",
      key: "code",
    },
    {
      title: "Description",
      dataIndex: "description",
      key: "description",
      render: (value) => value || "—",
    },
    {
      title: "Status",
      dataIndex: "is_active",
      key: "is_active",
      render: (value) => (
        <Tag color={value ? "green" : "red"}>
          {value ? "Active" : "Inactive"}
        </Tag>
      ),
    },
  ];

  return (
    <Card>
      <Title level={2}>Departments</Title>

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
          dataSource={departments}
          pagination={{ pageSize: 10 }}
          locale={{ emptyText: "No departments found." }}
          scroll={{ x: true }}
        />
      </Spin>
    </Card>
  );
}

export default Departments;