import { useEffect, useState } from "react";
import { Alert, Card, Spin, Table, Tag, Typography } from "antd";
import api from "../api/client";

const { Title } = Typography;

export default function Departments() {
Updated upstream;
const [departments, setDepartments] = useState([]);
const [loading, setLoading] = useState(true);
const [error, setError] = useState("");

useEffect(() => {
let active = true;

async function loadDepartments() {
  try {
    const response = await api.get("/departments");
    const payload = response.data && response.data;

    const rows = Array.isArray(payload)
      ? payload
      : Array.isArray(payload && payload.data)
        ? payload.data
        : [];

    if (active) setDepartments(rows);
  } catch (err) {
    if (active) {
      setError(
        err.response && response.data && message ||
          "Failed to load departments. Check the backend connection and your permissions."
      );
    }
  } finally {
    if (active) setLoading(false);
  }
}

loadDepartments();

return () => {
  active = false;
};

}, []);

const columns = [
{ title: "Department", dataIndex: "name", key: "name" },
{ title: "Code", dataIndex: "code", key: "code" },
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
      scroll={{ x: true }}
      locale={{ emptyText: "No departments found." }}
    />
  </Spin>
</Card>

);
  const [departments, setDepartments] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");

  useEffect(() => {
    let active = true;

    async function fetchDepartments() {
      try {
        setLoading(true);
        setError("");

        const response = await api.get("/departments");
        const payload = response.data && response.data;

        const rows = Array.isArray(payload)
          ? payload
          : Array.isArray(payload && payload.data)
            ? payload.data
            : [];

        if (active) {
          setDepartments(rows);
        }
      } catch (err) {
        if (active) {
          setError(
            err.response && response.data && message ||
              "Failed to load departments. Check the backend and your account permissions."
          );
        }
      } finally {
        if (active) {
          setLoading(false);
        }
      }
    }

    fetchDepartments();

    return () => {
      active = false;
    };
  }, []);

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
          scroll={{ x: true }}
          locale={{ emptyText: "No departments found." }}
        />
      </Spin>
    </Card>
  );
}