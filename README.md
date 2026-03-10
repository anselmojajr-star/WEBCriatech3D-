# Tectheta — Field Operations Data Platform

![Status](https://img.shields.io/badge/Status-Desenvolvimento_Ativo-success)
![Architecture](https://img.shields.io/badge/Architecture-MVC_%2B_Offline_First-orange)
![Data Platform](https://img.shields.io/badge/Data_Platform-End_to_End-blue)

---

## 📌 Visão Geral

**Tectheta** é uma plataforma desenvolvida para gestão e análise de operações de engenharia de campo.

O projeto integra três camadas principais:

- Sistema web administrativo para gestão operacional  
- Aplicação móvel para recolha de dados em campo  
- Pipeline de dados para análise e indicadores operacionais  

A plataforma foi projetada para funcionar em cenários onde a conectividade é limitada, garantindo **integridade e rastreabilidade dos dados desde a recolha até à camada analítica**.

---

## 🧱 Arquitetura da Plataforma

Aplicação Mobile (Flutter)
│
│ Sincronização segura
▼
API Backend (PHP)
│
│ Processamento transacional
▼
Base de Dados Operacional
│
│ Modelação analítica
▼
Camada de Business Intelligence


Princípios de arquitetura utilizados:

- **Offline-First Mobile Architecture**
- **Front Controller Backend Pattern**
- **Arquitetura orientada a APIs**
- **Princípio DRY (Don't Repeat Yourself)**
- **Processamento transacional ACID**

---

## ⚙️ Principais Capacidades da Plataforma

### Operações de Campo

- Gestão de equipas operacionais
- Registo de execução de atividades
- Captura de evidências visuais (fotografia e vídeo)
- Georreferenciação de operações
- Auditoria operacional baseada em localização

### Gestão Operacional

- Planeamento e acompanhamento de projetos
- Gestão de recursos e equipas
- Monitorização da execução de serviços
- Controlo de medições operacionais

### Camada Analítica

- Consolidação de dados operacionais
- Modelação de indicadores de desempenho
- Integração com ferramentas de Business Intelligence
- Monitorização de produção e custos operacionais

---

## 📱 Arquitetura Mobile

O aplicativo móvel foi desenvolvido com foco em operações de campo e ambientes com conectividade limitada.

Características principais:

- armazenamento local em **SQLite**
- operação **offline-first**
- sincronização automática quando há conectividade
- captura estruturada de dados operacionais

---

## 🛠️ Stack Tecnológica

### Backend

- PHP  
- REST APIs  
- Composer  

### Mobile

- Flutter / Dart  
- SQLite (armazenamento local)

### Frontend

- JavaScript  
- AJAX  
- Bootstrap / AdminLTE  

### Dados

- SQL  
- MySQL / PostgreSQL  
- Modelação analítica  

### Integrações

- APIs de geolocalização  
- ferramentas de Business Intelligence  

---

## 🖥️ Interface do Sistema

### Dashboard Operacional

![Dashboard](capturas/dashboard.png)

---

### Gestão de Projetos

![Projetos](capturas/projetos.png)

---

### Monitorização em Mapa

![Mapa](capturas/mapa.png)

---

## 💡 Sobre o Projeto

Este repositório apresenta uma visão arquitetural do projeto **Tectheta**, desenvolvido de forma independente como demonstração prática de competências em:

- arquitetura de software  
- engenharia de dados  
- integração de sistemas  
- plataformas operacionais para campo  

O objetivo é demonstrar capacidade de conceção e implementação de **plataformas de dados e sistemas operacionais integrados**.

---

Desenvolvido por  
https://www.linkedin.com/in/josemourajr/
