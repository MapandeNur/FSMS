import { useState } from "react";
import { Form, Input, Button, message } from "antd";
import { useNavigate } from "react-router-dom";
import { useAuth } from "../context/AuthContext";
import nssfLogo from "../assets/nssf-logo.png";

const Login = () => {
  const [loading, setLoading] = useState(false);
  const { login } = useAuth();
  const navigate = useNavigate();

  const onFinish = async (values) => {
    setLoading(true);
    try {
      await login(values.email, values.password);
      message.success("Welcome again!");
      navigate("/dashboard");
    } catch (err) {
      message.error("Login failed. Check email/password.");
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
      {/* Top bar */}
      <div
        style={{
          display: "flex",
          justifyContent: "space-between",
          alignItems: "center",
          padding: "20px 32px",
          color: "#f0e0c0",
        }}
      >
        <span style={{ fontSize: 15, opacity: 0.85 }}>
          Field Student Management System
        </span>
        <span
          style={{
            border: "1px solid #f9b233",
            color: "#f9b233",
            borderRadius: 6,
            padding: "4px 12px",
            fontSize: 13,
          }}
        >
          Production
        </span>
      </div>

      {/* Center card */}
      <div
        style={{
          flex: 1,
          display: "flex",
          alignItems: "center",
          justifyContent: "center",
        }}
      >
        <div
          style={{
            background: "#fff",
            borderRadius: 10,
            width: 420,
            padding: "40px 40px 24px",
            borderTop: "5px solid #f9b233",
            boxShadow: "0 20px 60px rgba(0,0,0,0.35)",
          }}
        >
          <div style={{ textAlign: "center", marginBottom: 24 }}>
            <img
               src={nssfLogo}
               alt="NSSF Logo"
               style={{
                width:100,
                height:100,
                objectFit:"contain",
                display:"block",
                margin:"0 auto 16px",
               }}
            />   
            <h1
              style={{
                fontFamily: "Georgia, 'Times New Roman', serif",
                fontSize: 30,
                margin: 0,
                color: "#222",
              }}
            >
              FSMS Portal
            </h1>
            <p style={{ color: "#777", marginTop: 8 }}>
              Field placements & student records for NSSF Trainee.
            </p>
          </div>

          <Form layout="vertical" onFinish={onFinish}>
            <Form.Item
              name="email"
              label="Email"
              rules={[{ required: true, message: "Email Required" }]}
            >
              <Input size="large" placeholder />
            </Form.Item>
            <Form.Item
              name="password"
              label="Password"
              rules={[{ required: true, message: "Password Required" }]}
            >
              <Input.Password size="large" />
            </Form.Item>
            <Button
              type="primary"
              htmlType="submit"
              loading={loading}
              block
              size="large"
              style={{
                background: "#f9b233",
                borderColor: "#f9b233",
                color: "#222",
                fontWeight: 600,
              }}
            >
              Sign in
            </Button>
          </Form>

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
            Access is limited to registered students and staff.
          </div>
        </div>
      </div>

      {/* Footer */}
      <div
        style={{
          textAlign: "center",
          color: "#e0c9a6",
          fontSize: 13,
          padding: "16px 0",
          opacity: 0.8,
        }}
      >
        Report a problem · System status · NSSF Computing Faculty, 2026
      </div>
    </div>
  );
};

export default Login;