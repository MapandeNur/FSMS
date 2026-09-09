import { Row, Col, Card, Statistic, Typography, Space } from "antd";
import {
  TeamOutlined,
  EnvironmentOutlined,
  FileDoneOutlined,
  ClockCircleOutlined,
} from "@ant-design/icons";
import { useAuth } from "../context/AuthContext";

const { Title, Text } = Typography;

const Dashboard = () => {
  const { user } = useAuth();

  const stats = [
    {
      title: "Total Students",
      value: 0,
      icon: <TeamOutlined />,
      color: "#1677ff",
    },
    {
      title: "Field Placements",
      value: 0,
      icon: <EnvironmentOutlined />,
      color: "#52c41a",
    },
    {
      title: "Reports Submitted",
      value: 0,
      icon: <FileDoneOutlined />,
      color: "#faad14",
    },
    {
      title: "Pending Reviews",
      value: 0,
      icon: <ClockCircleOutlined />,
      color: "#ff4d4f",
    },
  ];

  return (
    <div>
      {/* Welcome banner */}
      <div
        style={{
          background: "linear-gradient(135deg, #1677ff 0%, #003eb3 100%)",
          borderRadius: 12,
          padding: "24px 32px",
          marginBottom: 24,
          color: "#fff",
        }}
      >
        <Title level={3} style={{ color: "#fff", marginBottom: 4 }}>
          Welcome{user?.name ?  `,${user.name}` : ""} 👋
        </Title>
        <Text style={{ color: "rgba(255,255,255,0.85)" }}>
          Field Student Management System — preview of your activities.
        </Text>
      </div>

      {/* Stat cards */}
      <Row gutter={[16, 16]}>
        {stats.map((stat, index) => (
          <Col xs={24} sm={12} lg={6} key={index}>
            <Card bordered={false} style={{ borderRadius: 12 }}>
              <Space align="start" style={{ width: "100%", justifyContent: "space-between" }}>
                <Statistic
                  title={stat.title}
                  value={stat.value}
                  valueStyle={{ fontSize: 28, fontWeight: 600 }}
                />
                <div
                  style={{
                    fontSize: 22,
                    color: stat.color,
                    background: `${stat.color}1A`,
                    borderRadius: 10,
                    padding: 10,
                  }}
                >
                  {stat.icon}
                </div>
              </Space>
            </Card>
          </Col>
        ))}
      </Row>

      {/* Placeholder section for future content */}
      <Card
        bordered={false}
        style={{ marginTop: 24, borderRadius: 12, minHeight: 200 }}
        title="Current activities"
      >
        <Text type="secondary">
          Other team members will add their details.
        </Text>
      </Card>
    </div>
  );
};

export default Dashboard;