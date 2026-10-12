import React, { useEffect, useState } from "react";
import {
  Card,
  Col,
  Row,
  Statistic,
  Typography,
  Button,
  Progress,
  Tag,
  Avatar,
  Space,
  Spin,
  Alert,
} from "antd";

import {
  TeamOutlined,
  EnvironmentOutlined,
  FileDoneOutlined,
  ClockCircleOutlined,
  UserAddOutlined,
  PlusOutlined,
  ArrowRightOutlined,
  CheckCircleOutlined,
  SyncOutlined,
  UserOutlined,
} from "@ant-design/icons";

import { useNavigate } from "react-router-dom";
import { useAuth } from "../context/AuthContext";
import api from "../api/client";

const { Title, Text } = Typography;

const MAROON = "#6e1423";
const GOLD = "#f9b233";

const Dashboard = () => {
  const navigate = useNavigate();
  const { user, userRole } = useAuth();

  const [students, setStudents] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");

  const roleLabel = {
    admin: "Administrator",
    supervisor: "Supervisor",
    hr_officer: "HR Officer",
    student: "Student",
  };

  useEffect(() => {
    fetchStudents();
  }, []);

  const fetchStudents = async () => {
    try {
      setLoading(true);
      setError("");

      const response = await api.get("/v1/students");

      const responseData = response.data?.data;

      // Laravel paginated Resource response
      if (Array.isArray(responseData)) {
        setStudents(responseData);
      } else if (Array.isArray(responseData?.data)) {
        setStudents(responseData.data);
      } else {
        setStudents([]);
      }
    } catch (err) {
      console.error("DASHBOARD STUDENTS ERROR:", err);
      setError("Failed to load student statistics.");
    } finally {
      setLoading(false);
    }
  };

  // Real statistics from API
  const totalStudents = students.length;

  const activeStudents = students.filter(
    (student) => student.status === "active"
  ).length;

  const completedStudents = students.filter(
    (student) => student.status === "completed"
  ).length;

  const pendingStudents = students.filter(
    (student) => student.status === "pending"
  ).length;

  const activePercentage =
    totalStudents > 0
      ? Math.round((activeStudents / totalStudents) * 100)
      : 0;

  const completedPercentage =
    totalStudents > 0
      ? Math.round((completedStudents / totalStudents) * 100)
      : 0;

  const pendingPercentage =
    totalStudents > 0
      ? Math.round((pendingStudents / totalStudents) * 100)
      : 0;

  const stats = [
    {
      title: "Total Students",
      value: totalStudents,
      description: "Registered in the system",
      icon: <TeamOutlined />,
      action: () => navigate("/students"),
    },
    {
      title: "Field Placements",
      value: 0,
      description: "Active placements",
      icon: <EnvironmentOutlined />,
    },
    {
      title: "Reports Submitted",
      value: 0,
      description: "Student reports",
      icon: <FileDoneOutlined />,
    },
    {
      title: "Pending Reviews",
      value: 0,
      description: "Awaiting review",
      icon: <ClockCircleOutlined />,
    },
  ];

  return (
    <div
      style={{
        maxWidth: 1500,
        margin: "0 auto",
      }}
    >
      {/* WELCOME HEADER */}
      <Card
        bordered={false}
        style={{
          borderRadius: 18,
          marginBottom: 24,
          overflow: "hidden",
          boxShadow: "0 6px 20px rgba(0,0,0,0.06)",
          background: "#fff",
        }}
        styles={{
          body: {
            padding: 0,
          },
        }}
      >
        <div
          style={{
            minHeight: 170,
            padding: "30px 34px",
            display: "flex",
            alignItems: "center",
            justifyContent: "space-between",
            gap: 24,
            position: "relative",
            overflow: "hidden",
          }}
        >
          <div
            style={{
              position: "absolute",
              width: 220,
              height: 220,
              borderRadius: "50%",
              right: -80,
              top: -100,
              background: "rgba(249,178,51,0.12)",
            }}
          />

          <div
            style={{
              position: "absolute",
              width: 150,
              height: 150,
              borderRadius: "50%",
              right: 80,
              bottom: -100,
              background: "rgba(110,20,35,0.05)",
            }}
          />

          <div style={{ position: "relative", zIndex: 1 }}>
            <Tag
              style={{
                border: "none",
                background: "#fff4d9",
                color: MAROON,
                fontWeight: 600,
                padding: "5px 12px",
                borderRadius: 20,
                marginBottom: 10,
              }}
            >
              {roleLabel[userRole] || "System User"}
            </Tag>

            <Title
              level={2}
              style={{
                margin: "0 0 6px",
                color: MAROON,
                fontWeight: 700,
              }}
            >
              Welcome back, {user?.name || "User"} 👋
            </Title>

            <Text style={{ color: "#777", fontSize: 14 }}>
              Here is an overview of your Field Student Management System.
            </Text>
          </div>

          <div
            style={{
              position: "relative",
              zIndex: 1,
              display: "flex",
              alignItems: "center",
            }}
          >
            <Avatar
              size={76}
              icon={<UserOutlined />}
              style={{
                background: MAROON,
                color: GOLD,
                fontSize: 32,
                border: "5px solid #fff4d9",
              }}
            />
          </div>
        </div>
      </Card>

      {/* ERROR */}
      {error && (
        <Alert
          message={error}
          type="error"
          showIcon
          closable
          style={{
            marginBottom: 20,
            borderRadius: 10,
          }}
        />
      )}

      {/* STATISTICS */}
      <Row gutter={[18, 18]}>
        {stats.map((stat) => (
          <Col xs={24} sm={12} xl={6} key={stat.title}>
            <Card
              bordered={false}
              hoverable={Boolean(stat.action)}
              onClick={stat.action}
              style={{
                borderRadius: 16,
                height: "100%",
                cursor: stat.action ? "pointer" : "default",
                boxShadow: "0 4px 16px rgba(0,0,0,0.05)",
              }}
              styles={{
                body: {
                  padding: 22,
                },
              }}
            >
              <div
                style={{
                  display: "flex",
                  justifyContent: "space-between",
                  alignItems: "flex-start",
                  marginBottom: 18,
                }}
              >
                <div
                  style={{
                    width: 50,
                    height: 50,
                    borderRadius: 14,
                    background: "#fff4d9",
                    color: MAROON,
                    display: "flex",
                    alignItems: "center",
                    justifyContent: "center",
                    fontSize: 24,
                  }}
                >
                  {stat.icon}
                </div>

                {stat.action && (
                  <ArrowRightOutlined
                    style={{
                      color: "#aaa",
                      fontSize: 14,
                    }}
                  />
                )}
              </div>

              {loading ? (
                <div
                  style={{
                    height: 65,
                    display: "flex",
                    alignItems: "center",
                  }}
                >
                  <Spin size="small" />
                </div>
              ) : (
                <Statistic
                  title={
                    <span
                      style={{
                        color: "#777",
                        fontSize: 13,
                      }}
                    >
                      {stat.title}
                    </span>
                  }
                  value={stat.value}
                  valueStyle={{
                    color: MAROON,
                    fontSize: 30,
                    fontWeight: 750,
                  }}
                />
              )}

              <Text type="secondary" style={{ fontSize: 12 }}>
                {stat.description}
              </Text>
            </Card>
          </Col>
        ))}
      </Row>

      {/* MAIN CONTENT */}
      <Row gutter={[18, 18]} style={{ marginTop: 18 }}>
        {/* QUICK ACTIONS - ADMIN ONLY */}
        {userRole === "admin" && (
          <Col xs={24} lg={8}>
            <Card
              bordered={false}
              title={
                <div>
                  <div
                    style={{
                      color: MAROON,
                      fontSize: 17,
                      fontWeight: 700,
                    }}
                  >
                    Quick Actions
                  </div>

                  <Text type="secondary" style={{ fontSize: 12 }}>
                    Frequently used actions
                  </Text>
                </div>
              }
              style={{
                height: "100%",
                borderRadius: 16,
                boxShadow: "0 4px 16px rgba(0,0,0,0.05)",
              }}
            >
              <Space
                direction="vertical"
                size={12}
                style={{ width: "100%" }}
              >
                <Button
                  type="primary"
                  icon={<UserAddOutlined />}
                  block
                  size="large"
                  onClick={() => navigate("/users")}
                  style={{
                    background: MAROON,
                    borderColor: MAROON,
                    height: 48,
                    borderRadius: 10,
                  }}
                >
                  Manage Users
                </Button>

                <Button
                  icon={<PlusOutlined />}
                  block
                  size="large"
                  onClick={() => navigate("/students/register")}
                  style={{
                    height: 48,
                    borderRadius: 10,
                    borderColor: GOLD,
                    color: MAROON,
                  }}
                >
                  Register Student
                </Button>

                <Button
                  icon={<TeamOutlined />}
                  block
                  size="large"
                  onClick={() => navigate("/students")}
                  style={{
                    height: 48,
                    borderRadius: 10,
                  }}
                >
                  View Students
                </Button>
              </Space>
            </Card>
          </Col>
        )}

        {/* STUDENT STATUS */}
        <Col xs={24} lg={userRole === "admin" ? 8 : 12}>
          <Card
            bordered={false}
            title={
              <div>
                <div
                  style={{
                    color: MAROON,
                    fontSize: 17,
                    fontWeight: 700,
                  }}
                >
                  Student Overview
                </div>

                <Text type="secondary" style={{ fontSize: 12 }}>
                  Current student status
                </Text>
              </div>
            }
            style={{
              height: "100%",
              borderRadius: 16,
              boxShadow: "0 4px 16px rgba(0,0,0,0.05)",
            }}
          >
            <div style={{ marginBottom: 18 }}>
              <div
                style={{
                  display: "flex",
                  justifyContent: "space-between",
                  marginBottom: 8,
                }}
              >
                <Text>Active Students</Text>

                <Text strong style={{ color: MAROON }}>
                  {activeStudents}
                </Text>
              </div>

              <Progress
                percent={activePercentage}
                showInfo={false}
                strokeColor={MAROON}
                trailColor="#f0f0f0"
              />
            </div>

            <div style={{ marginBottom: 18 }}>
              <div
                style={{
                  display: "flex",
                  justifyContent: "space-between",
                  marginBottom: 8,
                }}
              >
                <Text>Completed</Text>

                <Text strong style={{ color: "#777" }}>
                  {completedStudents}
                </Text>
              </div>

              <Progress
                percent={completedPercentage}
                showInfo={false}
                strokeColor={GOLD}
                trailColor="#f0f0f0"
              />
            </div>

            <div>
              <div
                style={{
                  display: "flex",
                  justifyContent: "space-between",
                  marginBottom: 8,
                }}
              >
                <Text>Inactive</Text>

                <Text strong style={{ color: "#777" }}>
                  {pendingStudents}
                </Text>
              </div>

              <Progress
                percent={pendingPercentage}
                showInfo={false}
                strokeColor="#aaa"
                trailColor="#f0f0f0"
              />
            </div>
          </Card>
        </Col>

        {/* SYSTEM STATUS */}
        <Col xs={24} lg={userRole === "admin" ? 8 : 12}>
          <Card
            bordered={false}
            title={
              <div>
                <div
                  style={{
                    color: MAROON,
                    fontSize: 17,
                    fontWeight: 700,
                  }}
                >
                  System Status
                </div>

                <Text type="secondary" style={{ fontSize: 12 }}>
                  Current system information
                </Text>
              </div>
            }
            style={{
              height: "100%",
              borderRadius: 16,
              boxShadow: "0 4px 16px rgba(0,0,0,0.05)",
            }}
          >
            <Space
              direction="vertical"
              size={18}
              style={{ width: "100%" }}
            >
              <div
                style={{
                  display: "flex",
                  justifyContent: "space-between",
                  alignItems: "center",
                }}
              >
                <Space>
                  <CheckCircleOutlined
                    style={{
                      color: MAROON,
                      fontSize: 20,
                    }}
                  />

                  <Text>Authentication</Text>
                </Space>

                <Tag
                  style={{
                    color: MAROON,
                    background: "#fff4d9",
                    border: "none",
                    borderRadius: 20,
                  }}
                >
                  Active
                </Tag>
              </div>

              <div
                style={{
                  display: "flex",
                  justifyContent: "space-between",
                  alignItems: "center",
                }}
              >
                <Space>
                  <SyncOutlined
                    style={{
                      color: GOLD,
                      fontSize: 20,
                    }}
                  />

                  <Text>API Connection</Text>
                </Space>

                <Tag
                  style={{
                    color: "#527a52",
                    background: "#edf7ed",
                    border: "none",
                    borderRadius: 20,
                  }}
                >
                  Connected
                </Tag>
              </div>

              <div
                style={{
                  display: "flex",
                  justifyContent: "space-between",
                  alignItems: "center",
                }}
              >
                <Space>
                  <TeamOutlined
                    style={{
                      color: MAROON,
                      fontSize: 20,
                    }}
                  />

                  <Text>Student Records</Text>
                </Space>

                <Text strong style={{ color: MAROON }}>
                  {loading ? "..." : totalStudents}
                </Text>
              </div>
            </Space>
          </Card>
        </Col>
      </Row>

      {/* RECENT ACTIVITY */}
      <Card
        bordered={false}
        style={{
          marginTop: 18,
          borderRadius: 16,
          boxShadow: "0 4px 16px rgba(0,0,0,0.05)",
        }}
        title={
          <div>
            <div
              style={{
                color: MAROON,
                fontSize: 17,
                fontWeight: 700,
              }}
            >
              Recent Activity
            </div>

            <Text type="secondary" style={{ fontSize: 12 }}>
              Latest activity in the system
            </Text>
          </div>
        }
      >
        <Row gutter={[20, 20]}>
          <Col xs={24} md={8}>
            <div
              style={{
                display: "flex",
                alignItems: "center",
                gap: 12,
              }}
            >
              <Avatar
                icon={<UserOutlined />}
                style={{
                  background: "#fff4d9",
                  color: MAROON,
                }}
              />

              <div>
                <Text strong>Student registration</Text>

                <div>
                  <Text
                    type="secondary"
                    style={{ fontSize: 12 }}
                  >
                    Student records are connected to the API.
                  </Text>
                </div>
              </div>
            </div>
          </Col>

          <Col xs={24} md={8}>
            <div
              style={{
                display: "flex",
                alignItems: "center",
                gap: 12,
              }}
            >
              <Avatar
                icon={<CheckCircleOutlined />}
                style={{
                  background: "#fff4d9",
                  color: MAROON,
                }}
              />

              <div>
                <Text strong>Authentication</Text>

                <div>
                  <Text
                    type="secondary"
                    style={{ fontSize: 12 }}
                  >
                    User authentication is active.
                  </Text>
                </div>
              </div>
            </div>
          </Col>

          <Col xs={24} md={8}>
            <div
              style={{
                display: "flex",
                alignItems: "center",
                gap: 12,
              }}
            >
              <Avatar
                icon={<EnvironmentOutlined />}
                style={{
                  background: "#fff4d9",
                  color: MAROON,
                }}
              />

              <div>
                <Text strong>Field placements</Text>

                <div>
                  <Text
                    type="secondary"
                    style={{ fontSize: 12 }}
                  >
                    Placement module is ready for implementation.
                  </Text>
                </div>
              </div>
            </div>
          </Col>
        </Row>
      </Card>
    </div>
  );
};

export default Dashboard;
