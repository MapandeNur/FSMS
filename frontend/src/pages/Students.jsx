import React, { useMemo, useState } from "react";
import {
  Button,
  Card,
  Col,
  Input,
  Row,
  Select,
  Space,
  Table,
  Tag,
  Typography,
} from "antd";
import {
  PlusOutlined,
  SearchOutlined,
  EyeOutlined,
  ReloadOutlined,
} from "@ant-design/icons";
import { useNavigate } from "react-router-dom";

const { Title, Text } = Typography;

const Students = () => {
  const navigate = useNavigate();

  const [searchText, setSearchText] = useState("");
  const [genderFilter, setGenderFilter] = useState("all");
  const [statusFilter, setStatusFilter] = useState("all");
  const [institutionFilter, setInstitutionFilter] = useState("all");

  // Temporary sample data
  const students = [
    {
      key: "1",
      registration_number: "FS/2026/0001",
      name: "Huseini Miraji Hemedi",
      gender: "Male",
      institution: "University of Dar es Salaam",
      programme: "Information Technology",
      year: "Year 2",
      status: "Active",
    },
    {
      key: "2",
      registration_number: "FS/2026/0002",
      name: "Amina Hassan",
      gender: "Female",
      institution: "Ardhi University",
      programme: "Computer Science",
      year: "Year 3",
      status: "Active",
    },
    {
      key: "3",
      registration_number: "FS/2026/0003",
      name: "John Mgosi Manyuki",
      gender: "Male",
      institution: "University of Dodoma",
      programme: "Information Technology",
      year: "Year 2",
      status: "Completed",
    },
    {
      key: "4",
      registration_number: "FS/2026/0004",
      name: "Rebeka Mapande",
      gender: "Female",
      institution: "Mzumbe University",
      programme: "Business Information Systems",
      year: "Year 3",
      status: "Pending",
    },
  ];

  // Get unique institutions
  const institutions = [
    ...new Set(students.map((student) => student.institution)),
  ];

  // Apply filters
  const filteredStudents = useMemo(() => {
    return students.filter((student) => {
      const search = searchText.toLowerCase().trim();

      const matchesSearch =
        student.name.toLowerCase().includes(search) ||
        student.registration_number
          .toLowerCase()
          .includes(search) ||
        student.institution.toLowerCase().includes(search);

      const matchesGender =
        genderFilter === "all" ||
        student.gender.toLowerCase() ===
          genderFilter.toLowerCase();

      const matchesStatus =
        statusFilter === "all" ||
        student.status.toLowerCase() ===
          statusFilter.toLowerCase();

      const matchesInstitution =
        institutionFilter === "all" ||
        student.institution === institutionFilter;

      return (
        matchesSearch &&
        matchesGender &&
        matchesStatus &&
        matchesInstitution
      );
    });
  }, [
    searchText,
    genderFilter,
    statusFilter,
    institutionFilter,
  ]);

  // Reset filters
  const handleReset = () => {
    setSearchText("");
    setGenderFilter("all");
    setStatusFilter("all");
    setInstitutionFilter("all");
  };

  const columns = [
    {
      title: "Registration No.",
      dataIndex: "registration_number",
      key: "registration_number",
      render: (value) => (
        <Text strong style={{ color: "#6e1423" }}>
          {value}
        </Text>
      ),
    },
    {
      title: "Student Name",
      dataIndex: "name",
      key: "name",
    },
    {
      title: "Gender",
      dataIndex: "gender",
      key: "gender",
    },
    {
      title: "Institution",
      dataIndex: "institution",
      key: "institution",
    },
    {
      title: "Programme",
      dataIndex: "programme",
      key: "programme",
    },
    {
      title: "Year",
      dataIndex: "year",
      key: "year",
    },
    {
      title: "Status",
      dataIndex: "status",
      key: "status",
      render: (status) => {
        let color = "default";

        if (status === "Active") {
          color = "green";
        } else if (status === "Pending") {
          color = "orange";
        } else if (status === "Completed") {
          color = "blue";
        }

        return <Tag color={color}>{status}</Tag>;
      },
    },
    {
      title: "Action",
      key: "action",
      render: (_, record) => (
        <Button
          type="link"
          icon={<EyeOutlined />}
          onClick={() =>
            navigate(`/students/${record.key}`)
          }
        >
          View
        </Button>
      ),
    },
  ];

  return (
    <div>
      {/* PAGE HEADER */}
      <Row
        justify="space-between"
        align="middle"
        style={{ marginBottom: 24 }}
      >
        <Col>
          <Title
            level={2}
            style={{
              marginBottom: 4,
              color: "#6e1423",
            }}
          >
            Students
          </Title>

          <Text type="secondary">
            Manage registered field students
          </Text>
        </Col>

        <Col>
          <Button
            type="primary"
            icon={<PlusOutlined />}
            onClick={() =>
              navigate("/students/register")
            }
            style={{
              background: "#6e1423",
              borderColor: "#6e1423",
              height: 42,
              borderRadius: 8,
            }}
          >
            Register Student
          </Button>
        </Col>
      </Row>

      {/* SEARCH & FILTERS */}
      <Card
        bordered={false}
        style={{
          marginBottom: 20,
          borderRadius: 14,
        }}
      >
        <Row gutter={[16, 16]}>
          {/* SEARCH */}
          <Col xs={24} lg={8}>
            <Input
              size="large"
              prefix={<SearchOutlined />}
              placeholder="Search student, registration number..."
              value={searchText}
              onChange={(e) =>
                setSearchText(e.target.value)
              }
              allowClear
            />
          </Col>

          {/* GENDER */}
          <Col xs={24} sm={12} lg={4}>
            <Select
              size="large"
              style={{ width: "100%" }}
              value={genderFilter}
              onChange={setGenderFilter}
              options={[
                {
                  value: "all",
                  label: "All Genders",
                },
                {
                  value: "male",
                  label: "Male",
                },
                {
                  value: "female",
                  label: "Female",
                },
              ]}
            />
          </Col>

          {/* STATUS */}
          <Col xs={24} sm={12} lg={4}>
            <Select
              size="large"
              style={{ width: "100%" }}
              value={statusFilter}
              onChange={setStatusFilter}
              options={[
                {
                  value: "all",
                  label: "All Status",
                },
                {
                  value: "active",
                  label: "Active",
                },
                {
                  value: "pending",
                  label: "Pending",
                },
                {
                  value: "completed",
                  label: "Completed",
                },
              ]}
            />
          </Col>

          {/* INSTITUTION */}
          <Col xs={24} sm={12} lg={5}>
            <Select
              size="large"
              style={{ width: "100%" }}
              value={institutionFilter}
              onChange={setInstitutionFilter}
              placeholder="Institution"
              options={[
                {
                  value: "all",
                  label: "All Institutions",
                },
                ...institutions.map((institution) => ({
                  value: institution,
                  label: institution,
                })),
              ]}
            />
          </Col>

          {/* RESET */}
          <Col xs={24} sm={12} lg={3}>
            <Button
              size="large"
              icon={<ReloadOutlined />}
              onClick={handleReset}
              style={{
                width: "100%",
              }}
            >
              Reset
            </Button>
          </Col>
        </Row>
      </Card>

      {/* RESULT COUNT */}
      <div style={{ marginBottom: 12 }}>
        <Text type="secondary">
          Showing{" "}
          <strong>{filteredStudents.length}</strong>{" "}
          student
          {filteredStudents.length !== 1 ? "s" : ""}
        </Text>
      </div>

      {/* TABLE */}
      <Card
        bordered={false}
        style={{
          borderRadius: 14,
        }}
      >
        <Table
          columns={columns}
          dataSource={filteredStudents}
          scroll={{ x: 1100 }}
          pagination={{
            pageSize: 10,
            showSizeChanger: false,
          }}
        />
      </Card>
    </div>
  );
};

export default Students;