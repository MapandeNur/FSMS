import React, { useState } from "react";

import {
  Button,
  Card,
  Col,
  DatePicker,
  Form,
  Input,
  Row,
  Select,
  Space,
  Typography,
  message,
} from "antd";

import {
  ArrowLeftOutlined,
  SaveOutlined,
} from "@ant-design/icons";

import { useNavigate } from "react-router-dom";
import api from "../api/client";

const { Title, Text } = Typography;

const StudentRegistration = () => {
  const navigate = useNavigate();
  const [form] = Form.useForm();
  const [submitting, setSubmitting] = useState(false);

  // =========================
  // SUBMIT STUDENT
  // =========================
  const handleSubmit = async (values) => {
    setSubmitting(true);

    try {
      const payload = {
        first_name: values.first_name.trim(),

        middle_name: values.middle_name
          ? values.middle_name.trim()
          : null,

        last_name: values.last_name.trim(),

        // IMPORTANT:
        // Backend expects: male, female, other
        gender: values.gender,

        date_of_birth: values.date_of_birth.format(
          "YYYY-MM-DD"
        ),

        email: values.email.trim(),

        phone: values.phone.trim(),

        institution_name:
          values.institution_name.trim(),

        programme_of_study:
          values.programme_of_study.trim(),

        year_of_study:
          Number(values.year_of_study),

        start_date:
          values.start_date.format("YYYY-MM-DD"),

        end_date:
          values.end_date.format("YYYY-MM-DD"),

        status: values.status,
      };

     

      const response = await api.post(
        "/v1/students",
        payload
      );

      const student =
        response.data?.data;

      message.success(
        response.data?.message ||
          "Student registered successfully."
      );

      form.resetFields();

      navigate("/students", {
        state: {
          registeredStudent: student,
        },
      });
    } catch (error) {
      console.error(
        "STUDENT REGISTRATION ERROR:",
        error
      );

      const responseData =
        error.response?.data;

      // Laravel validation errors
      if (
        error.response?.status === 422 &&
        responseData?.errors
      ) {
        const fieldErrors =
          Object.entries(
            responseData.errors
          ).map(([field, errors]) => ({
            name: field,
            errors: Array.isArray(errors)
              ? errors
              : [String(errors)],
          }));

        form.setFields(fieldErrors);

        message.error(
          responseData.message ||
            "Please correct the highlighted fields."
        );
      } else {
        message.error(
          responseData?.message ||
            "Failed to register student. Please try again."
        );
      }
    } finally {
      setSubmitting(false);
    }
  };

  // =========================
  // DISABLE FUTURE DOB
  // =========================
  const disableFutureDates = (current) => {
    return (
      current &&
      current.isAfter(
        new Date(),
        "day"
      )
    );
  };

  // =========================
  // DISABLE END DATES
  // =========================
  const disableEndDates = (current) => {
    const startDate =
      form.getFieldValue("start_date");

    if (!startDate) {
      return false;
    }

    return (
      current &&
      current.isBefore(
        startDate,
        "day"
      )
    );
  };

  return (
    <div>
      {/* =========================
          HEADER
      ========================= */}
      <div
        style={{
          display: "flex",
          alignItems: "center",
          marginBottom: 24,
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
            Register Student
          </Title>

          <Text type="secondary">
            Add a new field student to the system
          </Text>
        </div>
      </div>

      {/* =========================
          FORM
      ========================= */}
      <Card
        bordered={false}
        style={{
          borderRadius: 14,
        }}
      >
        <Form
          form={form}
          layout="vertical"
          onFinish={handleSubmit}
          autoComplete="off"
          scrollToFirstError
        >
          {/* =========================
              PERSONAL INFORMATION
          ========================= */}
          <Title
            level={4}
            style={{
              color: "#6e1423",
              marginBottom: 20,
            }}
          >
            Personal Information
          </Title>

          <Row gutter={[20, 0]}>
            {/* FIRST NAME */}
            <Col xs={24} md={8}>
              <Form.Item
                label="First Name"
                name="first_name"
                rules={[
                  {
                    required: true,
                    message:
                      "Please enter first name",
                  },
                  {
                    pattern:
                      /^[A-Za-zÀ-ÿ]+(?:['-][A-Za-zÀ-ÿ]+)*$/,
                    message:
                      "Name can only contain letters, hyphens and apostrophes",
                  },
                  {
                    max: 50,
                    message:
                      "First name must not exceed 50 characters",
                  },
                ]}
              >
                <Input
                  size="large"
                  placeholder="Enter first name"
                />
              </Form.Item>
            </Col>

            {/* MIDDLE NAME */}
            <Col xs={24} md={8}>
              <Form.Item
                label="Middle Name"
                name="middle_name"
                rules={[
                  {
                    pattern:
                      /^[A-Za-zÀ-ÿ]+(?:['-][A-Za-zÀ-ÿ]+)*$/,
                    message:
                      "Name can only contain letters, hyphens and apostrophes",
                  },
                  {
                    max: 50,
                    message:
                      "Middle name must not exceed 50 characters",
                  },
                ]}
              >
                <Input
                  size="large"
                  placeholder="Enter middle name"
                />
              </Form.Item>
            </Col>

            {/* LAST NAME */}
            <Col xs={24} md={8}>
              <Form.Item
                label="Last Name"
                name="last_name"
                rules={[
                  {
                    required: true,
                    message:
                      "Please enter last name",
                  },
                  {
                    pattern:
                      /^[A-Za-zÀ-ÿ]+(?:['-][A-Za-zÀ-ÿ]+)*$/,
                    message:
                      "Name can only contain letters, hyphens and apostrophes",
                  },
                  {
                    max: 50,
                    message:
                      "Last name must not exceed 50 characters",
                  },
                ]}
              >
                <Input
                  size="large"
                  placeholder="Enter last name"
                />
              </Form.Item>
            </Col>

            {/* GENDER */}
            <Col xs={24} md={8}>
              <Form.Item
                label="Gender"
                name="gender"
                rules={[
                  {
                    required: true,
                    message:
                      "Please select gender",
                  },
                ]}
              >
                <Select
                  size="large"
                  placeholder="Select gender"
                  options={[
                    {
                      value: "male",
                      label: "Male",
                    },
                    {
                      value: "female",
                      label: "Female",
                    },
                    {
                      value: "other",
                      label: "Other",
                    },
                  ]}
                />
              </Form.Item>
            </Col>

            {/* DATE OF BIRTH */}
            <Col xs={24} md={8}>
              <Form.Item
                label="Date of Birth"
                name="date_of_birth"
                rules={[
                  {
                    required: true,
                    message:
                      "Please select date of birth",
                  },
                ]}
              >
                <DatePicker
                  size="large"
                  style={{
                    width: "100%",
                  }}
                  placeholder="Select date of birth"
                  disabledDate={
                    disableFutureDates
                  }
                />
              </Form.Item>
            </Col>

            {/* REGISTRATION NUMBER */}
            <Col xs={24} md={8}>
              <Form.Item
                label="Registration Number"
              >
                <Input
                  size="large"
                  value="Auto-generated by system"
                  disabled
                />
              </Form.Item>
            </Col>
          </Row>

          {/* =========================
              CONTACT INFORMATION
          ========================= */}
          <Title
            level={4}
            style={{
              color: "#6e1423",
              marginTop: 25,
              marginBottom: 20,
            }}
          >
            Contact Information
          </Title>

          <Row gutter={[20, 0]}>
            {/* EMAIL */}
            <Col xs={24} md={12}>
              <Form.Item
                label="Email"
                name="email"
                rules={[
                  {
                    required: true,
                    message:
                      "Please enter email",
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
                  placeholder="student@example.com"
                />
              </Form.Item>
            </Col>

            {/* PHONE */}
            <Col xs={24} md={12}>
              <Form.Item
                label="Phone"
                name="phone"
                rules={[
                  {
                    required: true,
                    message:
                      "Please enter phone number",
                  },
                  {
                    pattern:
                      /^(?:\+255|0)[67]\d{8}$/,
                    message:
                      "Enter a valid Tanzania phone number",
                  },
                ]}
              >
                <Input
                  size="large"
                  placeholder="0712345678"
                />
              </Form.Item>
            </Col>
          </Row>

          {/* =========================
              ACADEMIC INFORMATION
          ========================= */}
          <Title
            level={4}
            style={{
              color: "#6e1423",
              marginTop: 25,
              marginBottom: 20,
            }}
          >
            Academic Information
          </Title>

          <Row gutter={[20, 0]}>
            {/* INSTITUTION */}
            <Col xs={24} md={8}>
              <Form.Item
                label="Institution"
                name="institution_name"
                rules={[
                  {
                    required: true,
                    message:
                      "Please enter institution",
                  },
                  {
                    max: 255,
                    message:
                      "Institution name is too long",
                  },
                ]}
              >
                <Input
                  size="large"
                  placeholder="Enter institution"
                />
              </Form.Item>
            </Col>

            {/* PROGRAMME */}
            <Col xs={24} md={8}>
              <Form.Item
                label="Programme"
                name="programme_of_study"
                rules={[
                  {
                    required: true,
                    message:
                      "Please enter programme",
                  },
                  {
                    max: 255,
                    message:
                      "Programme name is too long",
                  },
                ]}
              >
                <Input
                  size="large"
                  placeholder="e.g. Information Technology"
                />
              </Form.Item>
            </Col>

            {/* YEAR */}
            <Col xs={24} md={8}>
              <Form.Item
                label="Year of Study"
                name="year_of_study"
                rules={[
                  {
                    required: true,
                    message:
                      "Please select year of study",
                  },
                ]}
              >
                <Select
                  size="large"
                  placeholder="Select year"
                  options={[
                    {
                      value: 1,
                      label: "Year 1",
                    },
                    {
                      value: 2,
                      label: "Year 2",
                    },
                    {
                      value: 3,
                      label: "Year 3",
                    },
                    {
                      value: 4,
                      label: "Year 4",
                    },
                    {
                      value: 5,
                      label: "Year 5",
                    },
                    {
                      value: 6,
                      label: "Year 6",
                    },
                    {
                      value: 7,
                      label: "Year 7",
                    },
                  ]}
                />
              </Form.Item>
            </Col>
          </Row>

          {/* =========================
              FIELD PLACEMENT
          ========================= */}
          <Title
            level={4}
            style={{
              color: "#6e1423",
              marginTop: 25,
              marginBottom: 20,
            }}
          >
            Field Placement
          </Title>

          <Row gutter={[20, 0]}>
            {/* START DATE */}
            <Col xs={24} md={8}>
              <Form.Item
                label="Start Date"
                name="start_date"
                rules={[
                  {
                    required: true,
                    message:
                      "Please select start date",
                  },
                ]}
              >
                <DatePicker
                  size="large"
                  style={{
                    width: "100%",
                  }}
                  onChange={() => {
                    form.validateFields([
                      "end_date",
                    ]);
                  }}
                />
              </Form.Item>
            </Col>

            {/* END DATE */}
            <Col xs={24} md={8}>
              <Form.Item
                label="End Date"
                name="end_date"
                dependencies={[
                  "start_date",
                ]}
                rules={[
                  {
                    required: true,
                    message:
                      "Please select end date",
                  },
                  ({ getFieldValue }) => ({
                    validator(_, value) {
                      const startDate =
                        getFieldValue(
                          "start_date"
                        );

                      if (
                        !value ||
                        !startDate ||
                        value.isAfter(
                          startDate,
                          "day"
                        )
                      ) {
                        return Promise.resolve();
                      }

                      return Promise.reject(
                        new Error(
                          "End date must be after start date"
                        )
                      );
                    },
                  }),
                ]}
              >
                <DatePicker
                  size="large"
                  style={{
                    width: "100%",
                  }}
                  disabledDate={
                    disableEndDates
                  }
                />
              </Form.Item>
            </Col>

{/* STATUS */}
<Col xs={24} md={8}>
  <Form.Item
    label="Status"
    name="status"
    initialValue="active"
    rules={[
      {
        required: true,
        message: "Please select status",
      },
    ]}
  >
    <Select
      size="large"
      options={[
        {
          value: "active",
          label: "Active",
        },
        {
          value: "completed",
          label: "Completed",
        },
        {
          value: "inactive",
          label: "Inactive",
        },
      ]}
    />
  </Form.Item>
</Col>
          </Row>

          {/* =========================
              ACTIONS
          ========================= */}
          <Form.Item
            style={{
              marginTop: 20,
              marginBottom: 0,
            }}
          >
            <Space>
              <Button
                size="large"
                onClick={() =>
                  navigate("/students")
                }
                disabled={submitting}
              >
                Cancel
              </Button>

              <Button
                type="primary"
                htmlType="submit"
                icon={<SaveOutlined />}
                size="large"
                loading={submitting}
                style={{
                  background: "#6e1423",
                  borderColor: "#6e1423",
                }}
              >
                Register Student
              </Button>
            </Space>
          </Form.Item>
        </Form>
      </Card>
    </div>
  );
};

export default StudentRegistration;
