import React from "react";
import {
  Button,
  Card,
  Col,
  Descriptions,
  Row,
  Tag,
  Typography,
} from "antd";
import {
  ArrowLeftOutlined,
  EditOutlined,
} from "@ant-design/icons";
import { useNavigate, useParams } from "react-router-dom";

const { Title, Text } = Typography;

const StudentDetails = () => {
  const navigate = useNavigate();
  const { id } = useParams();

  // Temporary sample data
  // Later this will come from the Laravel API
  const students = {
    1: {
      registration_number: "FS/2026/0001",
      first_name: "Huseini",
      middle_name: "Miraji",
      last_name: "Hemedi",
      gender: "Male",
      date_of_birth: "2004-05-15",
      email: "huseini@example.com",
      phone: "0712345678",
      institution: "University of Dar es Salaam",
      programme: "Information Technology",
      year_of_study: "Year 2",
      start_date: "2026-06-01",
      end_date: "2026-09-30",
      status: "Active",
    },

    2: {
      registration_number: "FS/2026/0002",
      first_name: "Amina",
      middle_name: "Hassan",
      last_name: "",
      gender: "Female",
      date_of_birth: "2003-08-20",
      email: "amina@example.com",
      phone: "0755123456",
      institution: "Ardhi University",
      programme: "Computer Science",
      year_of_study: "Year 3",
      start_date: "2026-06-01",
      end_date: "2026-09-30",
      status: "Active",
    },

    3: {
      registration_number: "FS/2026/0003",
      first_name: "John",
      middle_name: "Mgosi",
      last_name: "Manyuki",
      gender: "Male",
      date_of_birth: "2004-02-10",
      email: "john@example.com",
      phone: "0788123456",
      institution: "University of Dodoma",
      programme: "Information Technology",
      year_of_study: "Year 2",
      start_date: "2026-06-01",
      end_date: "2026-09-30",
      status: "Completed",
    },

    4: {
      registration_number: "FS/2026/0004",
      first_name: "Rebeka",
      middle_name: "Mapande",
      last_name: "",
      gender: "Female",
      date_of_birth: "2003-11-25",
      email: "rebeka@example.com",
      phone: "0766123456",
      institution: "Mzumbe University",
      programme: "Business Information Systems",
      year_of_study: "Year 3",
      start_date: "2026-06-01",
      end_date: "2026-09-30",
      status: "Pending",
    },
  };

  const student = students[id];

  if (!student) {
    return (
      <div>
        <Button
          type="text"
          icon={<ArrowLeftOutlined />}
          onClick={() => navigate("/students")}
          style={{ color: "#6e1423", marginBottom: 20 }}
        >
          Back to Students
        </Button>

        <Card bordered={false} style={{ borderRadius: 14 }}>
          <Title level={3} style={{ color: "#6e1423" }}>
            Student Not Found
          </Title>

          <Text type="secondary">
            The student you are looking for does not exist.
          </Text>
        </Card>
      </div>
    );
  }

  const fullName = [
    student.first_name,
    student.middle_name,
    student.last_name,
  ]
    .filter(Boolean)
    .join(" ");

  let statusColor = "default";

  if (student.status === "Active") {
    statusColor = "green";
  } else if (student.status === "Pending") {
    statusColor = "orange";
  } else if (student.status === "Completed") {
    statusColor = "blue";
  }

  return (
    <div>
      {/* Header */}
      <Row
        justify="space-between"
        align="middle"
        style={{ marginBottom: 24 }}
      >
        <Col>
          <div style={{ display: "flex", alignItems: "center" }}>
            <Button
              type="text"
              icon={<ArrowLeftOutlined />}
              onClick={() => navigate("/students")}
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
                View student registration and field placement information
              </Text>
            </div>
          </div>
        </Col>

        <Col>
          <Button
            type="primary"
            icon={<EditOutlined />}
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

      {/* Student Summary */}
      <Card
        bordered={false}
        style={{
          borderRadius: 14,
          marginBottom: 20,
        }}
      >
        <Row gutter={[24, 24]} align="middle">
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
              {student.first_name.charAt(0)}
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

            <div style={{ marginTop: 10 }}>
              <Tag color={statusColor}>{student.status}</Tag>
            </div>
          </Col>
        </Row>
      </Card>

      {/* Personal Information */}
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

        <Descriptions bordered column={{ xs: 1, sm: 2, md: 2 }}>
          <Descriptions.Item label="Registration Number">
            {student.registration_number}
          </Descriptions.Item>

          <Descriptions.Item label="Full Name">
            {fullName}
          </Descriptions.Item>

          <Descriptions.Item label="Gender">
            {student.gender}
          </Descriptions.Item>

          <Descriptions.Item label="Date of Birth">
            {student.date_of_birth}
          </Descriptions.Item>

          <Descriptions.Item label="Email">
            {student.email}
          </Descriptions.Item>

          <Descriptions.Item label="Phone">
            {student.phone}
          </Descriptions.Item>
        </Descriptions>
      </Card>

      {/* Academic Information */}
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

        <Descriptions bordered column={{ xs: 1, sm: 2, md: 2 }}>
          <Descriptions.Item label="Institution">
            {student.institution}
          </Descriptions.Item>

          <Descriptions.Item label="Programme">
            {student.programme}
          </Descriptions.Item>

          <Descriptions.Item label="Year of Study">
            {student.year_of_study}
          </Descriptions.Item>
        </Descriptions>
      </Card>

      {/* Field Placement */}
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

        <Descriptions bordered column={{ xs: 1, sm: 2, md: 2 }}>
          <Descriptions.Item label="Start Date">
            {student.start_date}
          </Descriptions.Item>

          <Descriptions.Item label="End Date">
            {student.end_date}
          </Descriptions.Item>

          <Descriptions.Item label="Status">
            <Tag color={statusColor}>{student.status}</Tag>
          </Descriptions.Item>
        </Descriptions>
      </Card>
    </div>
  );
};

export default StudentDetails;