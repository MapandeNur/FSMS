import React, { useState } from "react";
import { Layout, Menu, Typography, Avatar, Divider } from "antd";
import {
  DashboardOutlined,
  TeamOutlined,
  EnvironmentOutlined,
  FileTextOutlined,
  UserOutlined,
  SafetyOutlined,
  SettingOutlined,
  LogoutOutlined,
  MenuFoldOutlined,
  MenuUnfoldOutlined,
} from "@ant-design/icons";
import { Outlet, useLocation, useNavigate } from "react-router-dom";
import { useAuth } from "../../context/AuthContext";

const { Sider, Content } = Layout;
const { Text } = Typography;

const MAROON = "#6e1423";
const GOLD = "#f9b233";

const MainLayout = () => {
  const [collapsed, setCollapsed] = useState(false);

  const location = useLocation();
  const navigate = useNavigate();
  const { user, logout } = useAuth();

  const handleLogout = () => {
    logout();
    navigate("/login");
  };

  const menuItems = [
    {
      key: "/dashboard",
      icon: <DashboardOutlined />,
      label: "Dashboard",
    },

    {
      type: "group",
      label: "STUDENT MANAGEMENT",
      children: [
        {
          key: "/students",
          icon: <TeamOutlined />,
          label: "Students",
        },
        {
          key: "/placements",
          icon: <EnvironmentOutlined />,
          label: "Field Placements",
        },
      ],
    },

    {
      type: "group",
      label: "REPORTS",
      children: [
        {
          key: "/reports",
          icon: <FileTextOutlined />,
          label: "Reports",
        },
      ],
    },

    {
      type: "group",
      label: "ADMINISTRATION",
      children: [
        {
          key: "/users",
          icon: <UserOutlined />,
          label: "Users",
        },
        {
          key: "/roles",
          icon: <SafetyOutlined />,
          label: "Roles & Permissions",
        },
        {
          key: "/settings",
          icon: <SettingOutlined />,
          label: "Settings",
        },
      ],
    },
  ];

  const handleMenuClick = ({ key }) => {
    navigate(key);
  };

  return (
    <Layout style={{ minHeight: "100vh" }}>
      {/* SIDEBAR */}
      <Sider
        collapsed={collapsed}
        trigger={null}
        width={220}
        collapsedWidth={72}
        style={{
          background: MAROON,
          position: "fixed",
          left: 0,
          top: 0,
          bottom: 0,
          zIndex: 1000,
          boxShadow: "2px 0 10px rgba(0,0,0,0.08)",
        }}
      >
        {/* BRAND */}
        <div
          style={{
            height: 70,
            display: "flex",
            alignItems: "center",
            justifyContent: collapsed ? "center" : "flex-start",
            padding: collapsed ? 0 : "0 16px",
          }}
        >
          <Avatar
            size={38}
            style={{
              background: GOLD,
              color: "#222",
              fontWeight: 800,
              flexShrink: 0,
            }}
          >
            FS
          </Avatar>

          {!collapsed && (
            <div style={{ marginLeft: 10, overflow: "hidden" }}>
              <div
                style={{
                  color: "#fff",
                  fontSize: 18,
                  fontWeight: 700,
                  lineHeight: 1.1,
                }}
              >
                FSMS
              </div>

              <Text
                style={{
                  color: "#f0e0c0",
                  fontSize: 10,
                  whiteSpace: "nowrap",
                }}
              >
                Field Student Management
              </Text>
            </div>
          )}
        </div>

        <Divider
          style={{
            borderColor: "rgba(255,255,255,0.18)",
            margin: "0 0 8px",
          }}
        />

        {/* NAVIGATION */}
        <Menu
          mode="inline"
          selectedKeys={[location.pathname]}
          items={menuItems}
          onClick={handleMenuClick}
          style={{
            background: "transparent",
            borderRight: 0,
          }}
          theme="dark"
        />

        {/* USER / LOGOUT */}
        <div
          style={{
            position: "absolute",
            bottom: 0,
            left: 0,
            right: 0,
            borderTop: "1px solid rgba(255,255,255,0.15)",
            padding: collapsed ? "12px 0" : "12px",
          }}
        >
          {!collapsed && (
            <div
              style={{
                display: "flex",
                alignItems: "center",
                marginBottom: 8,
                padding: "4px 6px",
              }}
            >
              <Avatar
                size={34}
                icon={<UserOutlined />}
                style={{
                  background: GOLD,
                  color: "#222",
                }}
              />

              <div
                style={{
                  marginLeft: 9,
                  overflow: "hidden",
                }}
              >
                <div
                  style={{
                    color: "#fff",
                    fontWeight: 600,
                    fontSize: 13,
                    whiteSpace: "nowrap",
                    overflow: "hidden",
                    textOverflow: "ellipsis",
                  }}
                >
                  {user?.name || "System User"}
                </div>

                <div
                  style={{
                    color: "#f0e0c0",
                    fontSize: 11,
                    whiteSpace: "nowrap",
                    overflow: "hidden",
                    textOverflow: "ellipsis",
                  }}
                >
                  {user?.email || "User account"}
                </div>
              </div>
            </div>
          )}

          <Menu
            mode="inline"
            theme="dark"
            selectable={false}
            items={[
              {
                key: "logout",
                icon: <LogoutOutlined />,
                label: "Logout",
              },
            ]}
            onClick={handleLogout}
            style={{
              background: "transparent",
              border: 0,
            }}
          />
        </div>
      </Sider>

      {/* MAIN AREA */}
      <Layout
        style={{
          marginLeft: collapsed ? 72 : 220,
          transition: "all 0.2s",
          minHeight: "100vh",
        }}
      >
        {/* HEADER */}
        <div
          style={{
            height: 64,
            background: "#fff",
            borderBottom: "1px solid #eee",
            display: "flex",
            alignItems: "center",
            padding: "0 22px",
            position: "sticky",
            top: 0,
            zIndex: 900,
          }}
        >
          <div
            onClick={() => setCollapsed(!collapsed)}
            style={{
              fontSize: 19,
              cursor: "pointer",
              color: MAROON,
              display: "flex",
              alignItems: "center",
            }}
          >
            {collapsed ? (
              <MenuUnfoldOutlined />
            ) : (
              <MenuFoldOutlined />
            )}
          </div>

          <div
            style={{
              marginLeft: 18,
              borderLeft: `3px solid ${GOLD}`,
              paddingLeft: 12,
            }}
          >
            <Text
              strong
              style={{
                fontSize: 15,
                color: MAROON,
              }}
            >
              Field Student Management System
            </Text>
          </div>
        </div>

        {/* PAGE CONTENT */}
        <Content
          style={{
            padding: 24,
            background: "#f8f5f2",
            minHeight: "calc(100vh - 64px)",
          }}
        >
          <Outlet />
        </Content>
      </Layout>
    </Layout>
  );
};

export default MainLayout;

