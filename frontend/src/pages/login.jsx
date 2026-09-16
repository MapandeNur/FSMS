
import React, { useState } from "react";
import {
  Form,
  Input,
  Button,
  message,
  Typography,
  Divider,
} from "antd";
import {
  ArrowLeftOutlined,
  UserAddOutlined,
  LoginOutlined,
} from "@ant-design/icons";
import { useNavigate, useLocation } from "react-router-dom";
import { useAuth } from "../context/AuthContext";
import api from "../api/axios";


const { Text } = Typography;

const Login = () => {
  const [loading, setLoading] = useState(false);
  const [showRegister, setShowRegister] = useState(false);

  const { login } = useAuth();
  const navigate = useNavigate();
  const location = useLocation();

  const from = location.state?.from?.pathname || "/dashboard";

  // =========================
  // LOGIN
  // =========================
  const onLogin = async (values) => {
    setLoading(true);

    try {
      await login(values.email, values.password);

      message.success("Welcome again!");

      navigate(from, { replace: true });
    } catch (error) {
      console.error("Login error:", error);

      console.error(
        "Login response:",
        error.response?.data
      );

      message.error(
        error.response?.data?.message ||
          "Login failed. Please check your email and password."
      );
    } finally {
      setLoading(false);
    }
  };

  // =========================
  // REGISTRATION
  // =========================
  const onRegister = async (values) => {
  setLoading(true);

  try {
    const response = await api.post(
      "/v1/auth/register",
      {
        name: values.name,
        email: values.email,
        phone: values.phone,
        password: values.password,
        password_confirmation: values.password_confirmation,
      }
    );

    console.log("REGISTRATION RESPONSE:", response.data);

    message.success(
      response.data?.message ||
        "Registration successful. You can now login."
    );

    setShowRegister(false);
  } catch (error) {
    console.error("REGISTRATION FAILED:", error);
    console.error(
      "Registration response:",
      error.response?.data
    );

    const errors = error.response?.data?.errors;

    if (errors) {
      const firstError = Object.values(errors).flat()[0];

      message.error(
        firstError ||
          error.response?.data?.message ||
          "Registration failed."
      );
    } else {
      message.error(
        error.response?.data?.message ||
          "Registration failed. Please try again."
      );
    }
  } finally {
    setLoading(false);
  }
};

  return (
    <div
      style={{
        minHeight: "100vh",
        background: "#6e1423",
        backgroundImage:
          "radial-gradient(circle, rgba(255,255,255,0.05) 1px, transparent 1px)",
        backgroundSize: "24px 24px",
        display: "flex",
        flexDirection: "column",
      }}
    >
      {/* =========================
          TOP BAR
      ========================== */}
      <div
        style={{
          display: "flex",
          justifyContent: "space-between",
          alignItems: "center",
          padding: "20px 32px",
          color: "#f0e0c0",
        }}
      >
        <span
          style={{
            fontSize: 15,
            opacity: 0.9,
          }}
        >
          Field Student Management System
        </span>

        <span
          style={{
            border: "1px solid #f9b233",
            color: "#f9b233",
            borderRadius: 6,
            padding: "4px 12px",
            fontSize: 13,
            fontWeight: 500,
          }}
        >
          Production
        </span>
      </div>

      {/* =========================
          CENTER
      ========================== */}
      <div
        style={{
          flex: 1,
          display: "flex",
          alignItems: "center",
          justifyContent: "center",
          padding: "30px 20px",
        }}
      >
        <div
          style={{
            background: "#fff",
            borderRadius: 12,
            width: "100%",
            maxWidth: 440,
            padding: "36px 40px 24px",
            borderTop: "5px solid #f9b233",
            boxShadow: "0 20px 60px rgba(0,0,0,0.35)",
          }}
        >
          {/* =========================
              LOGO + TITLE
          ========================== */}
          <div
            style={{
              textAlign: "center",
              marginBottom: 24,
            }}
          >
           

            <h1
              style={{
                fontFamily:
                  "Georgia, 'Times New Roman', serif",
                fontSize: 30,
                margin: 0,
                color: "#222",
              }}
            >
              {showRegister
                ? "Create Account"
                : "FSMS Portal"}
            </h1>

            <p
              style={{
                color: "#777",
                marginTop: 8,
                marginBottom: 0,
              }}
            >
              {showRegister
                ? "Register your account to access the FSMS portal."
                : "Field placements & student records for NSSF Trainee."}
            </p>
          </div>

          {/* =========================
              LOGIN
          ========================== */}
          {!showRegister && (
            <>
              <Form
                layout="vertical"
                onFinish={onLogin}
                requiredMark={false}
              >
                <Form.Item
                  name="email"
                  label="Email"
                  rules={[
                    {
                      required: true,
                      message: "Email is required",
                    },
                    {
                      type: "email",
                      message:
                        "Please enter a valid email",
                    },
                  ]}
                >
                  <Input
                    size="large"
                    placeholder="Enter your email"
                  />
                </Form.Item>

                <Form.Item
                  name="password"
                  label="Password"
                  rules={[
                    {
                      required: true,
                      message: "Password is required",
                    },
                  ]}
                >
                  <Input.Password
                    size="large"
                    placeholder="Enter your password"
                  />
                </Form.Item>

                <Button
                  type="primary"
                  htmlType="submit"
                  loading={loading}
                  block
                  size="large"
                  icon={<LoginOutlined />}
                  style={{
                    background: "#f9b233",
                    borderColor: "#f9b233",
                    color: "#222",
                    fontWeight: 600,
                    height: 46,
                  }}
                >
                  Sign in
                </Button>
              </Form>

              {/* CREATE ACCOUNT */}
              <div
                style={{
                  textAlign: "center",
                  marginTop: 20,
                }}
              >
                <Text type="secondary">
                  Don't have an account?{" "}
                </Text>

                <Button
                  type="link"
                  icon={<UserAddOutlined />}
                  onClick={() => setShowRegister(true)}
                  style={{
                    padding: 0,
                    fontWeight: 600,
                    color: "#6e1423",
                  }}
                >
                  Create Account
                </Button>
              </div>
            </>
          )}

          {/* =========================
              REGISTRATION
          ========================== */}
          {showRegister && (
            <>
              <Form
                layout="vertical"
                onFinish={onRegister}
                requiredMark={false}
              >
                <Form.Item
                  name="name"
                  label="Full Name"
                  rules={[
                    {
                      required: true,
                      message:
                        "Full name is required",
                    },
                  ]}
                >
                  <Input
                    size="large"
                    placeholder="Enter your full name"
                  />
                </Form.Item>

                <Form.Item
                  name="email"
                  label="Email"
                  rules={[
                    {
                      required: true,
                      message: "Email is required",
                    },
                    {
                      type: "email",
                      message:
                        "Please enter a valid email",
                    },
                  ]}
                >
                  <Input
                    size="large"
                    placeholder="Enter your email"
                  />
                </Form.Item>

                <Form.Item
                  name="phone"
                  label="Phone Number"
                  rules={[
                    {
                      required: true,
                      message:
                        "Phone number is required",
                    },
                  ]}
                >
                  <Input
                    size="large"
                    placeholder="Enter phone number"
                  />
                </Form.Item>

                <Form.Item
                  name="password"
                  label="Password"
                  rules={[
                    {
                      required: true,
                      message:
                        "Password is required",
                    },
                    {
                      min: 8,
                      message:
                        "Password must be at least 8 characters",
                    },
                  ]}
                >
                  <Input.Password
                    size="large"
                    placeholder="Create a password"
                  />
                </Form.Item>

                <Form.Item
                  name="password_confirmation"
                  label="Confirm Password"
                  dependencies={["password"]}
                  rules={[
                    {
                      required: true,
                      message:
                        "Please confirm your password",
                    },
                    ({ getFieldValue }) => ({
                      validator(_, value) {
                        if (
                          !value ||
                          getFieldValue("password") ===
                            value
                        ) {
                          return Promise.resolve();
                        }

                        return Promise.reject(
                          new Error(
                            "Passwords do not match"
                          )
                        );
                      },
                    }),
                  ]}
                >
                  <Input.Password
                    size="large"
                    placeholder="Confirm password"
                  />
                </Form.Item>

                <Button
                  type="primary"
                  htmlType="submit"
                  loading={loading}
                  block
                  size="large"
                  icon={<UserAddOutlined />}
                  style={{
                    background: "#f9b233",
                    borderColor: "#f9b233",
                    color: "#222",
                    fontWeight: 600,
                    height: 46,
                  }}
                >
                  Create Account
                </Button>
              </Form>

              <Divider
                style={{
                  margin: "20px 0 14px",
                }}
              />

              {/* BACK TO LOGIN */}
              <div
                style={{
                  textAlign: "center",
                }}
              >
                <Button
                  type="link"
                  icon={<ArrowLeftOutlined />}
                  onClick={() => setShowRegister(false)}
                  style={{
                    color: "#6e1423",
                    fontWeight: 600,
                  }}
                >
                  Back to Login
                </Button>
              </div>
            </>
          )}

          {/* =========================
              FOOTER NOTE
          ========================== */}
          {!showRegister && (
            <div
              style={{
                borderTop: "1px solid #eee",
                marginTop: 24,
                paddingTop: 16,
                textAlign: "center",
                color: "#999",
                fontSize: 13,
              }}
            >
              Access is limited to registered students
              and staff.
            </div>
          )}
        </div>
      </div>

      {/* =========================
          FOOTER
      ========================== */}
      <div
        style={{
          textAlign: "center",
          color: "#e0c9a6",
          fontSize: 13,
          padding: "16px 0",
          opacity: 0.8,
        }}
      >
        Report a problem · System status · NSSF Computing
        Faculty, 2026
      </div>
    </div>
  );
};

export default Login;

