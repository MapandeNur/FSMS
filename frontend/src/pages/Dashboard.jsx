import React from "react";
import {
  Card,
  Col,
  Row,
  Statistic,
  Typography,
  Space,
} from "antd";
import {
  TeamOutlined,
  EnvironmentOutlined,
  FileDoneOutlined,
  ClockCircleOutlined,
} from "@ant-design/icons";

const { Title, Text } = Typography;

const Dashboard = () => {
  const stats = [
    {
      title: "Total Students",
      value: 0,
      icon: <TeamOutlined />,
      description: "Registered students",
    },
    {
      title: "Field Placements",
      value: 0,
      icon: <EnvironmentOutlined />,
      description: "Active placements",
    },
    {
      title: "Reports Submitted",
      value: 0,
      icon: <FileDoneOutlined />,
      description: "Student reports",
    },
    {
      title: "Pending Reviews",
      value: 0,
      icon: <ClockCircleOutlined />,
      description: "Awaiting review",
    },
  ];

  return (
    <div>
      {/* Page Header */}
      <div style={{ marginBottom: 24 }}>
        <Title
          level={2}
          style={{
            marginBottom: 4,
            color: "#6e1423",
          }}
        >
          Dashboard
        </Title>

        <Text type="secondary">
          Welcome to the Field Student Management System
        </Text>
      </div>

      {/* Dashboard Cards */}
      <Row gutter={[20, 20]}>
        {stats.map((stat) => (
          <Col
            xs={24}
            sm={12}
            lg={6}
            key={stat.title}
          >
            <Card
              bordered={false}
              style={{
                borderRadius: 14,
                height: "100%",
                boxShadow: "0 4px 14px rgba(0, 0, 0, 0.06)",
              }}
              bodyStyle={{
                padding: 22,
              }}
            >
              <Space
                direction="vertical"
                size={12}
                style={{ width: "100%" }}
              >
                {/* Icon */}
                <div
                  style={{
                    width: 48,
                    height: 48,
                    borderRadius: 12,
                    background: "#f9b233",
                    display: "flex",
                    alignItems: "center",
                    justifyContent: "center",
                    fontSize: 24,
                    color: "#6e1423",
                  }}
                >
                  {stat.icon}
                </div>

                {/* Statistic */}
                <Statistic
                  title={stat.title}
                  value={stat.value}
                  valueStyle={{
                    color: "#6e1423",
                    fontWeight: 700,
                  }}
                />

                {/* Description */}
                <Text type="secondary">
                  {stat.description}
                </Text>
              </Space>
            </Card>
          </Col>
        ))}
      </Row>

      {/* Welcome Section */}
      <Card
        bordered={false}
        style={{
          marginTop: 24,
          borderRadius: 14,
          boxShadow: "0 4px 14px rgba(0, 0, 0, 0.05)",
        }}
      >
        <Title
          level={4}
          style={{
            marginTop: 0,
            color: "#6e1423",
          }}
        >
          Field Student Management System
        </Title>

        <Text type="secondary">
          Use the navigation menu to manage students, field
          placements, reports, users and system settings.
        </Text>
      </Card>
    </div>
  );
};

export default Dashboard;