import React, { useEffect, useState } from "react";
import {
  Button,
  Card,
  Col,
  Descriptions,
  Row,
  Spin,
  Tag,
  Typography,
  message,
} from "antd";
import {
  ArrowLeftOutlined,
  EditOutlined,
} from "@ant-design/icons";
import { useNavigate, useParams } from "react-router-dom";
import api from "../api/client";

const { Title, Text } = Typography;

const StudentDetails = () => {
  const navigate = useNavigate();
  const { id } = useParams();

  const [student, setStudent] = useState(null);
  const [loading, setLoading] = useState(true);

  // =========================
  // FETCH STUDENT FROM API
  // =========================
  useEffect(() => {
    const fetchStudent = async () => {
      setLoading(true);

      try {
        const response = await api.get(
          `/v1/students/${id}`
        );

        const data = response.data?.data;

        if (!data) {
          throw new Error("Student data was not returned.");
        }

        setStudent(data);
      } catch (error) {
        console.error(
          "Failed to fetch student:",
          error
        );

        message.error(
          error.response?.data?.message ||
            "Failed to load student details."
        );

        setStudent(null);
      } finally {
        setLoading(false);
      }
    };

    if (id) {
      fetchStudent();
    }
  }, [id]);

  // =========================
  // LOADING
  // =========================
  if (loading) {
    return (
      <div
        style={{
          display: "flex",
          justifyContent: "center",
          alignItems: "center",
          minHeight: 400,
        }}
      >
        <Spin size="large" />
      </div>
    );
  }

  // =========================
  // STUDENT NOT FOUND
  // =========================
  if (!student) {
    return (
      <div>
        <Button
          type="text"
          icon={<ArrowLeftOutlined />}
          onClick={() => navigate("/students")}
          style={{
            color: "#6e1423",
            marginBottom: 20,
          }}
        >
          Back to Students
        </Button>

        <Card
          bordered={false}
          style={{
            borderRadius: 14,
          }}
        >
          <Title
            level={3}
            style={{
              color: "#6e1423",
            }}
          >
            Student Not Found
          </Title>

          <Text type="secondary">
            The student you are looking for does not
            exist or could not be loaded.
          </Text>
        </Card>
      </div>
    );
  }

  // =========================
  // STUDENT INFORMATION
  // =========================
  const fullName = [
    student.first_name,
    student.middle_name,
    student.last_name,
  ]
    .filter(Boolean)
    .join(" ");

  const institution =
    student.institution_name || "-";

  const programme =
    student.programme_of_study || "-";

  const yearOfStudy =
    student.year_of_study
      ? `Year ${student.year_of_study}`
      : "-";

  // =========================
  // STATUS
  // =========================
  const normalizedStatus =
    student.status?.toLowerCase();

  let statusColor = "default";

  if (normalizedStatus === "active") {
    statusColor = "green";
  } else if (normalizedStatus === "pending") {
    statusColor = "orange";
  } else if (normalizedStatus === "completed") {
    statusColor = "blue";
  } else if (normalizedStatus === "inactive") {
    statusColor = "red";
  }

  const displayStatus =
    student.status
      ? student.status.charAt(0).toUpperCase() +
        student.status.slice(1)
      : "Unknown";

  return (
    <div>
      {/* =========================
          HEADER
      ========================= */}
      <Row
        justify="space-between"
        align="middle"
        style={{
          marginBottom: 24,
        }}
      >
        <Col>
          <div
            style={{
              display: "flex",
              alignItems: "center",
            }}
          >
            <Button
              type="text"
              icon={<ArrowLeftOutlined />}
              onClick={() =>
                navigate("/students")
              }
              style={{
                color: "#6e1423",
                marginRight: 10,
              }}
            />

            <div>
              <Title
                level={2}
                style={{
                  margin: 0,
                  color: "#6e1423",
                }}
              >
                Student Details
              </Title>

              <Text type="secondary">
                View student registration and field
                placement information
              </Text>
            </div>
          </div>
        </Col>

        <Col>
          <Button
            type="primary"
            icon={<EditOutlined />}
            onClick={() =>
              navigate(`/students/${student.id}/edit`)
            }
            style={{
              background: "#6e1423",
              borderColor: "#6e1423",
              borderRadius: 8,
            }}
          >
            Edit Student
          </Button>
        </Col>
      </Row>

      {/* =========================
          STUDENT SUMMARY
      ========================= */}
      <Card
        bordered={false}
        style={{
          borderRadius: 14,
          marginBottom: 20,
        }}
      >
        <Row
          gutter={[24, 24]}
          align="middle"
        >
          <Col xs={24} md={4}>
            <div
              style={{
                width: 90,
                height: 90,
                borderRadius: "50%",
                background: "#f9b233",
                color: "#6e1423",
                display: "flex",
                alignItems: "center",
                justifyContent: "center",
                fontSize: 32,
                fontWeight: 700,
              }}
            >
              {student.first_name
                ?.charAt(0)
                .toUpperCase()}
            </div>
          </Col>

          <Col xs={24} md={14}>
            <Title
              level={3}
              style={{
                margin: 0,
                color: "#6e1423",
              }}
            >
              {fullName}
            </Title>

            <Text type="secondary">
              {student.registration_number}
            </Text>

            <div
              style={{
                marginTop: 10,
              }}
            >
              <Tag color={statusColor}>
                {displayStatus}
              </Tag>
            </div>
          </Col>
        </Row>
      </Card>

      {/* =========================
          PERSONAL INFORMATION
      ========================= */}
      <Card
        bordered={false}
        style={{
          borderRadius: 14,
          marginBottom: 20,
        }}
      >
        <Title
          level={4}
          style={{
            color: "#6e1423",
            marginTop: 0,
          }}
        >
          Personal Information
        </Title>

        <Descriptions
          bordered
          column={{
            xs: 1,
            sm: 2,
            md: 2,
          }}
        >
          <Descriptions.Item label="Registration Number">
            {student.registration_number}
          </Descriptions.Item>

          <Descriptions.Item label="Full Name">
            {fullName}
          </Descriptions.Item>

          <Descriptions.Item label="Gender">
            {student.gender || "-"}
          </Descriptions.Item>

          <Descriptions.Item label="Date of Birth">
            {student.date_of_birth
              ? new Date(
                  student.date_of_birth
                ).toLocaleDateString()
              : "-"}
          </Descriptions.Item>

          <Descriptions.Item label="Email">
            {student.email || "-"}
          </Descriptions.Item>

          <Descriptions.Item label="Phone">
            {student.phone || "-"}
          </Descriptions.Item>
        </Descriptions>
      </Card>

      {/* =========================
          ACADEMIC INFORMATION
      ========================= */}
      <Card
        bordered={false}
        style={{
          borderRadius: 14,
          marginBottom: 20,
        }}
      >
        <Title
          level={4}
          style={{
            color: "#6e1423",
            marginTop: 0,
          }}
        >
          Academic Information
        </Title>

        <Descriptions
          bordered
          column={{
            xs: 1,
            sm: 2,
            md: 2,
          }}
        >
          <Descriptions.Item label="Institution">
            {institution}
          </Descriptions.Item>

          <Descriptions.Item label="Programme">
            {programme}
          </Descriptions.Item>

          <Descriptions.Item label="Year of Study">
            {yearOfStudy}
          </Descriptions.Item>
        </Descriptions>
      </Card>

      {/* =========================
          FIELD PLACEMENT
      ========================= */}
      <Card
        bordered={false}
        style={{
          borderRadius: 14,
        }}
      >
        <Title
          level={4}
          style={{
            color: "#6e1423",
            marginTop: 0,
          }}
        >
          Field Placement
        </Title>

        <Descriptions
          bordered
          column={{
            xs: 1,
            sm: 2,
            md: 2,
          }}
        >
          <Descriptions.Item label="Start Date">
            {student.start_date
              ? new Date(
                  student.start_date
                ).toLocaleDateString()
              : "-"}
          </Descriptions.Item>

          <Descriptions.Item label="End Date">
            {student.end_date
              ? new Date(
                  student.end_date
                ).toLocaleDateString()
              : "-"}
          </Descriptions.Item>

          <Descriptions.Item label="Status">
            <Tag color={statusColor}>
              {displayStatus}
            </Tag>
          </Descriptions.Item>
        </Descriptions>
      </Card>
    </div>
  );
};

export default StudentDetails;
