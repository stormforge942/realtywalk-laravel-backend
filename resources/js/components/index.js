import Vue from 'vue'
import DataTable from './DataTable'
import TreeSelect from './TreeSelect'
import TreeSelectPolygon from './TreeSelectPolygon'
import TreeSelectBuilderArea from './TreeSelectBuilderArea'
import TreeSelectBuilderAlias from './TreeSelectBuilderAlias'
import TreeSelectPolygonAreas from "./TreeSelectPolygonAreas";
import BuilderSelect from './BuilderSelect'
import InputMask from './InputMask'
import ColorPicker from './ColorPicker'
import CountupText from './CountupText'
import Dropzone from './Dropzone'
import PolyMap from './PolyMap'
/* charts */
import ChartPie from './ChartPie'
import ChartDoughnut from './ChartDoughnut'

[
  DataTable,
  Dropzone,
  TreeSelect,
  TreeSelectPolygon,
  TreeSelectPolygonAreas,
  TreeSelectBuilderArea,
  TreeSelectBuilderAlias,
  BuilderSelect,
  InputMask,
  ColorPicker,
  CountupText,
  ChartPie,
  ChartDoughnut,
  PolyMap
].forEach(Component => {
  Vue.component(Component.name, Component)
})
