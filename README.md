# Tectheta | Ecossistema de Engenharia de Dados e Gestão Operacional

![Status](https://img.shields.io/badge/Status-Desenvolvimento_Ativo-success)
![Data Engineering](https://img.shields.io/badge/Data_Engineering-Pipeline_%26_ETL-blue)
![Architecture](https://img.shields.io/badge/Architecture-MVC_%2B_Offline_First-orange)

## 📌 Visão Geral
O *tectheta* é um projeto prático e independente de arquitetura de dados e engenharia de software, desenvolvido para resolver o desafio de recolha, processamento e análise de dados em operações de engenharia de campo.

Mais do que um simples sistema de gestão, o Tectheta atua como uma *Plataforma de Dados End-to-End*: garante a integridade da informação desde a recolha no telemóvel (offline) em zonas remotas, passando pela orquestração de APIs e processamento transacional, até à entrega de métricas financeiras num dashboard de Business Intelligence.

---

## ⚙️ Arquitetura de Dados e Funcionalidades Principais

### 1. Ingestão de Dados e Automação (ETL/ELT)
* *Robô de Precificação (Web Scraping):* Algoritmo automatizado com rotação de User-Agent e pausas heurísticas para extração de preços de mercado, garantindo a atualização contínua da base de dados de materiais sem intervenção manual.
* *Sincronização Mobile-to-Cloud:* Arquitetura Offline-First. A equipa de campo recolhe dados via App (Flutter) numa base de dados local (SQLite). Quando há conectividade, um serviço de fila (Sync Service) processa o envio estruturado para a API via JWT, garantindo zero perda de pacotes.

### 2. Modelação e Processamento Backend
* *Arquitetura MVC & Front Controller:* O "motor" do sistema foi construído com PHP nativo gerido por Composer, focado em alta performance e no Princípio DRY (Don't Repeat Yourself).
* *Transações ACID:* O processamento de Ordens de Serviço (OS) e Medições Financeiras utiliza blocos transacionais rigorosos para evitar dados órfãos ou inconsistentes na base de dados relacional.
* *Vigência Histórica de Preços:* Modelação avançada que congela o valor financeiro de um item no tempo, garantindo que o recálculo de medições antigas no BI seja 100% preciso, mesmo com a flutuação atual de mercado.

### 3. Inteligência Geográfica (GIS)
* *Auditoria por Geofencing:* Integração com a API do Google Maps para monitorização espacial. O sistema processa as coordenadas GPS das fotos recolhidas em campo e emite um alerta automático se a evidência visual for capturada fora do raio planeado (>100 metros) da Ordem de Serviço.
* *Cercas Virtuais:* Desenho de polígonos no mapa armazenados via endpoints assíncronos para validação territorial de equipas.

### 4. Governança e Camada Analítica (BI)
* Os dados processados pelo Tectheta alimentam diretamente a camada semântica, onde modelos dimensionais (Star Schema) são consumidos pelo *Power BI* para gerar KPIs em tempo real para a diretoria, erradicando o uso de folhas de cálculo paralelas.

---

## 🛠️ Stack Tecnológico

*Engenharia de Dados e Base de Dados:*
* SQL (Avançado)
* MySQL / PostgreSQL (Cloud)
* SQLite (Mobile Storage)
* Python / PHP (Scripts de Ingestão e Web Scraping)

*Backend e APIs:*
* PHP Nativo (Arquitetura MVC Artesanal)
* RESTful APIs (Autenticação baseada em JWT)
* Composer (Gestão de Dependências)

*Frontend e Mobilidade:*
* Flutter / Dart (App Mobile Offline-First)
* JavaScript / AJAX (Live Search e Interatividade DOM)
* Google Maps API (Geoprocessamento)

---

## 💡 Sobre o Projeto
*Nota para Recrutadores:* Este repositório reflete a minha capacidade técnica como *Engenheiro de Dados e BI*. O projeto foi desenhado do zero, sem frameworks pesados, para demonstrar o domínio sobre arquitetura de sistemas, modelação de bases de dados relacionais e fluxos de integração contínua de informação. É um projeto de portfólio e de natureza não comercial.

---
# Tectheta Field Operations Platform

Sistema de gestão de operações de campo com integração web e mobile.

## Dashboard

![Dashboard](capturas de tela/dashboard.png)

## Gestão de Projetos

![Projetos](screenshots/projetos.png)

## Mapa Operacional

![Mapa](screenshots/mapa.png)


---
Desenvolvido com foco na integridade do dado por https://www.linkedin.com/in/josemourajr/
